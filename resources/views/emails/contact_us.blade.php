<!DOCTYPE html>
<html>
<head>
    <title>Contact Us Message</title>
</head>
<body>
    <p>Hello HR</p>
    <p>I hope this email finds you well.</p>
    <p>This email is to inform you that a support request has been submitted via the support form. Below are the details of the client who submitted the request:</p>
    <ul>
        <li><strong>Name:</strong> {{$name}}</li>
        <li><strong>Email:</strong> {{$email}}</li>
    </ul>
    <textarea disabled>{{$messageContent}}</textarea>
    <p>Please review the request and provide the necessary assistance at your earliest convenience.</p>
    
    <p>If you need any additional information, feel free to reach out.</p>
    <p>Best regards,</p>
    <p>Support Team</p>
    <p>support@tadafuq.ae</p>
   
</body>
</html>