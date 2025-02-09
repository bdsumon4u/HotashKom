#!/bin/bash

# Define the name and public key
KEY_NAME="GACD"
ssh_private_key="$HOME/.ssh/$KEY_NAME"

# Define the variables you want to extract
variables=("DB_USERNAME" "DB_DATABASE" "DB_PASSWORD")
# Read the .env file line by line
while IFS='=' read -r key value; do
    # Check if the key is one of the variables you want to extract
    if [[ " ${variables[@]} " =~ " $key " ]]; then
        # Remove leading/trailing whitespace from the value
        # value=$(echo "$value" | sed -e 's/^"//' -e 's/"$//' -e "s/^'//" -e "s/'$//")

        if [[ $value == \"*\" || $value == \'*\' ]]; then
            export "$key"=$value
        else
            export "$key"="$value"
        fi
    fi
done < ".env"

# Variables of `source` are now available in the shell

# Set default values if not provided as command-line arguments
default_root_dir="public_html"

# Parse command-line arguments
while [[ $# -gt 0 ]]; do
    case "$1" in
        -s|--site)
            target_site="$2"
            shift 2
            ;;
        -d|--domain)
            target_domain="$2"
            shift 2
            ;;
        -h|--host)
            ssh_host="$2"
            shift 2
            ;;
        -u|--uname)
            target_username="$2"
            shift 2
            ;;
        -db|--dbname)
            target_db_dbase="$2"
            shift 2
            ;;
        -dbu|--dbuser)
            target_db_uname="$2"
            shift 2
            ;;
        -dbp|--dbpass)
            target_db_upass="$2"
            shift 2
            ;;
        -mu|--mailuser)
            target_mail_user="$2"
            shift 2
            ;;
        -mp|--mailpass)
            target_mail_pass="$2"
            shift 2
            ;;
        -r|--rootdir)
            target_root_dir="$2"
            shift 2
            ;;
        *)
            echo "Unknown option: $1"
            exit 1
            ;;
    esac
done

# Prompt for missing values if not provided as arguments
[[ -z $target_site ]] && read -p "Enter target site name: " target_site
[[ -z $target_domain ]] && read -p "Enter target site domain: " target_domain
[[ -z $target_username ]] && read -p "Enter target cPanel username: " target_username
[[ -z $ssh_host ]] && read -p "Enter target server IP address: " ssh_host
[[ -z $target_db_dbase ]] && read -p "Enter target database name: " -ei "${target_username}_" target_db_dbase
[[ -z $target_db_uname ]] && read -p "Enter target database username: " -ei "${target_username}_" target_db_uname
[[ -z $target_db_upass ]] && read -p "Enter target database password: " target_db_upass
[[ -z $target_mail_user ]] && read -p "Enter target email address: " target_mail_user
[[ -z $target_mail_pass ]] && read -p "Enter target email password: " target_mail_pass
[[ -z $target_root_dir ]] && read -p "Enter target site root directory: " -ei "$default_root_dir" target_root_dir

# Function to add SSH host to known_hosts if not already present
add_ssh_host_to_known_hosts() {
    if ! grep -q "$ssh_host" ~/.ssh/known_hosts; then
        echo "Adding $ssh_host to known_hosts..."
        ssh-keyscan -H "$ssh_host" >> ~/.ssh/known_hosts
    fi
}

# Function to check if the public key is installed on the target server
check_public_key_installed() {
    # Check if the public key exists in the authorized_keys file on the server
    ssh -i $ssh_private_key $target_username@$ssh_host "grep -q '$(cat $ssh_private_key.pub)' .ssh/authorized_keys"
    
    # Return the exit status of the previous command
    return $?
}

# Function to connect to target server via SSH
connect_to_target() {
    # Attempt to connect to target server via SSH
    ssh -i $ssh_private_key $target_username@$ssh_host "exit"

    # Check the exit status of the previous command
    if [ $? -eq 0 ]; then
        echo "Successfully connected to the target server."
    else
        echo "Failed to connect to the target server."
        return 1
    fi
}

# Add SSH host to known_hosts if not already present
add_ssh_host_to_known_hosts

# Try to connect to the target server
connect_to_target || {
    # Check if the public key is installed on the target server
    check_public_key_installed
    
    # If the public key is not installed, prompt the user to add it
    if [ $? -ne 0 ]; then
        echo
        echo "Please add the following public key to the target server:"
        echo "Name: $KEY_NAME"
        echo "Public Key:"
        cat "$ssh_private_key.pub"
        echo
        read -p "Press Enter after authorizing the public key, or enter 'q' to quit: " response

        # If the user enters 'q', exit the script
        if [ "$response" = "q" ]; then
            exit 1
        fi
    fi
    
    # Retry connecting to the target server
    connect_to_target
}

# Transfer SSH Private Key to target
scp -i $ssh_private_key $ssh_private_key $target_username@$ssh_host:.ssh
ssh -i $ssh_private_key $target_username@$ssh_host "chmod 600 .ssh/$KEY_NAME"

# Transfer database and files from source to target
mysqldump -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE > database_backup.sql
zip -r -1 -y -9 site_backup.zip . -x "storage/app/pathao*" "storage/app/mpdf" "storage/debugbar" "storage/framework" "storage/logs"
scp -i $ssh_private_key site_backup.zip $target_username@$ssh_host:site_backup.zip
rm site_backup.zip database_backup.sql

# Unzip files, import database, update .env, and run deployment commands in a single SSH session
ssh -i $ssh_private_key $target_username@$ssh_host <<EOF
  # Unzip files and remove the backup
  unzip -o site_backup.zip -d $target_root_dir && rm site_backup.zip

  # Import the database
  cd $target_root_dir
  mysql -u $target_db_uname -p$target_db_upass $target_db_dbase < database_backup.sql && rm database_backup.sql

  # Update .env file
  sed -i "s/APP_NAME=.*/APP_NAME='$target_site'/" $target_root_dir/.env
  sed -i "s|APP_URL=.*|APP_URL=https://www.$target_domain|" $target_root_dir/.env
  sed -i "s/DB_DATABASE=.*/DB_DATABASE=$target_db_dbase/" $target_root_dir/.env
  sed -i "s/DB_USERNAME=.*/DB_USERNAME=$target_db_uname/" $target_root_dir/.env
  sed -i "s|DB_PASSWORD=.*|DB_PASSWORD='$(echo $target_db_upass | sed 's/|/\\|/g')'|" $target_root_dir/.env
  sed -i "s/MAIL_HOST=.*/MAIL_HOST=mail.$target_domain/" $target_root_dir/.env
  sed -i "s/MAIL_USERNAME=.*/MAIL_USERNAME=$target_mail_user/" $target_root_dir/.env
  sed -i "s|MAIL_PASSWORD=.*|MAIL_PASSWORD='$(echo $target_mail_pass | sed 's/|/\\|/g')'|" $target_root_dir/.env
  sed -i "s/MAIL_FROM_ADDRESS=.*/MAIL_FROM_ADDRESS=$target_mail_user/" $target_root_dir/.env

  # Run deployment commands
  cd $target_root_dir
  ./server_deploy.sh
  rm -rf public/storage storage/app/pathao*
  /opt/alt/php82/usr/bin/php artisan storage:link
  /opt/alt/php82/usr/bin/php artisan optimize:clear
EOF