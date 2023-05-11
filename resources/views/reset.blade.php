<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Online Library</title>

        <style>
            a{
                text-decoration: none;
                font-family: sans-serif;
                padding: 5px 10px;
                text-align: center;
                background-color: #0d6efd;
                color: #f8f9fa !important;
                border:1px solid transparent;
                border-radius: 4px;
                display: block;
                margin: 5px auto;
                max-width: 100px;
                transition:2s !important;
                font-size: 15px;
            }
            a:hover{
                background-color: #f8f9fa;
                color:#0d6efd !important;
                border:1px solid #0d6efd
            }
            p{
                font-size: 15px;
                padding-bottom: 12px;
                border-bottom: 1px solid #343a40;
                margin-bottom: 12px;
            }
            h3{
                margin-top: 0;
            }
            .container{
                box-shadow: 2px 3px #d3d4d7;
                padding: 10px 20px;
                color: #343a40;
                background-color: #f8f9fa;
                text-align: center;
                border-radius: 8px;
                margin: auto;
                margin-top: 25px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1 class="title">Hello Again</h1>

            <p>
                If you want to reset password please click to button down, If not you can ignore this message and thanks for your time
                <a href="http://localhost:5500/reset/{{$token}}" target="_blank">Click</a>
                Anyway we hope nice experience in our site
            </p>

            <h3>Library Team</h3>
        </div>
    </body>
</html>