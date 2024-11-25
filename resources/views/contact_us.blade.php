<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
    <link rel="icon" type="image/x-icon" href="{{asset('dashboard/logo.png')}}">
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            padding: 0px;
        }

        .home {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100vh;
            overflow: auto;
        }

        /* Container */
        .container {
            display: flex;
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Form Section */
        .form-section {
            flex: 1;
            padding: 40px;
        }

        .form-section h1 {
            color: #f15b2a;
            margin-bottom: 10px;
        }

        .form-section h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .form-section p {
            margin-bottom: 20px;
            color: #555;
        }

        .form-group {
            display: flex;
            gap: 10px;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        textarea {
            height: 100px;
        }

        .checkbox {
            margin-bottom: 20px;
        }

        .checkbox label {
            font-size: 14px;
        }

        .checkbox a {
            color: #f15b2a;
            text-decoration: none;
        }

        button {
            background-color: #f15b2a;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #d1441b;
        }

        /* Contact Details Section */
        .contact-details {
            flex: 1;
            background-color: #e6f4ff;
            padding: 40px;
            text-align: center;
        }

        .contact-details img {
            width: 70%;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .details {
            margin-top: 20px;
            font-size: 16px;
        }

        @media(max-width:990px) {
            .home {
                height: auto;
                margin-top: 5vh;
            }

            .container {
                flex-direction: column;
            }

            .form-section h2 {
                font-size: 20px;
                margin-bottom: 10px;
            }

            .form-section p {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="home">
        <div class="container">
            <div class="form-section">
                <div id="successAlert" class="alert alert-success"style=" display:none;padding-top:5px;padding-bottom:5px; padding-left: 10px; background-color:green;border-radius: 20px; color:beige;">Message Sent successfully</div>
                <h1>Get in Touch</h1>
                <h2>Let's Chat, Reach Out to Us</h2>
                <p>Have questions or feedback? We’re here to help. Send us a message, and we’ll respond within 24 hours.
                </p>
                <form action="#">
                    <div class="form-group">
                        <input id="first_name" type="text" placeholder="First Name" name="firstName" required>
                        <input id="last_name" type="text" placeholder="Last Name" name="lastName" required>
                    </div>
                    <input id="email" type="email" placeholder="Email Address" name="email" required>
                    <textarea id="message" placeholder="Leave us a message" name="message" required></textarea>
                    <div class="checkbox">
                        <input type="checkbox" id="privacy" required>
                        <label for="privacy">I agree to our friendly <a href="{{url('/privacy_policy')}}">privacy policy</a></label>
                    </div>
                    <button type="button" id="submitEvaluation">Send Message</button>
                </form>
            </div>
            <div class="contact-details">
                <img src="{{asset('/dashboard/contact_us.png')}}" alt="Contact Us">
                <div class="details">
                    <p><strong>Email:</strong>support@tadafuq.ae</p>
                </div>
            </div>
            
        </div>
        
       
    </div>
    
    
      
    <script>
        $(document).ready(function () {
    $('#submitEvaluation').on('click', function (e) {
        e.preventDefault();

        // Collect form data
        const formData = {
            first_name: $('#first_name').val(),
            last_name: $('#last_name').val(),
            email: $('#email').val(),
            message: $('#message').val(),
            _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
        };

        // AJAX request
        $.ajax({
            url: '/contact_us', // Update with your route URL
            type: 'POST',
            data: formData,
            success: function (response) {
                $('#successAlert').css('display', 'block');
             
              setTimeout(function() {
                $('#successAlert').css('display', 'none');
              }, 4000); 

            },
            error: function (xhr) {
                // Handle error response
                alert('Error: ' + xhr.responseJSON.message);
                console.error(xhr.responseJSON.errors);
            }
        });
    });
});
    </script>
</body>

</html>