@extends('layouts.yellow.master')

@title('Edit Profile')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Profile</h5>
                    </div>
                    <div class="card-divider"></div>
                    <div class="card-body">
                        <x-form method="POST" :action="route('user.profile.edit')">
                            @php($user = auth()->user())
                            <div class="row no-gutters">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <x-input name="name" id="name" placeholder="Full Name" :value="$user->name" />
                                        <x-error field="name" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="profile-email">Email Address</label>
                                        <x-input type="email" name="email" id="profile-email"
                                            placeholder="Email Address" :value="$user->email" />
                                        <x-error field="email" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="profile-phone">Phone Number</label>
                                        <x-input type="tel" name="phone_number" id="profile-phone"
                                            placeholder="Phone Number" :value="$user->phone_number" />
                                        <x-error field="phone_number" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <x-input name="address" id="address" placeholder="Enter Your Address"
                                            :value="$user->address" />
                                        <x-error field="address" />
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                    </div>
                    </x-form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
