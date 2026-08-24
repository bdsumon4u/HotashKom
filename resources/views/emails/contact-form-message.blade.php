<!doctype html>
<html>
<body style="margin:0;background:#f4f7f5;font-family:Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0"
       style="padding:30px 15px;background:#f4f7f5;">
<tr>
<td align="center">

<table width="100%" cellpadding="0" cellspacing="0"
       style="max-width:650px;background:#fff;border-radius:16px;overflow:hidden;">

<tr>
<td style="background:#101a16;color:#fff;padding:25px 30px;">
    <div style="color:#79d8aa;font-size:12px;font-weight:bold;">
        {{ strtoupper(data_get(setting('company'), 'name') ?: config('app.name')) }} CUSTOMER CARE
    </div>

    <h2 style="margin:8px 0 0;">
        New Contact Message
    </h2>
</td>
</tr>

<tr>
<td style="padding:30px;color:#101828;line-height:1.7;">

    <p>
        <strong>Name:</strong><br>
        {{ $contact['name'] }}
    </p>

    <p>
        <strong>Email:</strong><br>
        {{ $contact['email'] }}
    </p>

    <p>
        <strong>Subject:</strong><br>
        {{ $contact['subject'] }}
    </p>

    <p>
        <strong>Message:</strong>
    </p>

    <div style="
        background:#f7faf8;
        border:1px solid #e5ebe8;
        border-radius:10px;
        padding:18px;
        white-space:pre-line;
    ">{{ $contact['message'] }}</div>

    <p style="margin-top:25px;color:#667085;font-size:12px;">
        Sent from {{ data_get(setting('company'), 'name') ?: config('app.name') }} Contact Us page
    </p>

</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>