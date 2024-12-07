<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graduate</title>
    <style>
        /* Full page background with a meteor effect */
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background-image: url('https://media.giphy.com/media/jaOXKCxtBPLieRLI0c/giphy.gif'); /* Meteor GIF background */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        /* Header animation */
        h1 {
            position: fixed;
            top: 10px; /* Position the header at the top */
            font-size: 3rem;
            color: white;
            animation: moveLeft 15s linear infinite;
        }

        @keyframes moveLeft {
            0% {
                left: 100%;
            }
            50% {
                left: -100%;
            }
            100% {
                left: 100%;
            }
        }

        /* Centered Image */
        .center-image {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 100%;
            max-height: 80%; /* Adjust to fit within the viewport */
        }

        .center-image img {
            max-width: 50%;
            max-height: 50%;
        }

        /* Footer Caption */
        footer {
            text-align: center;
            color: white;
            font-size: 1.5rem;
            padding: 10px;
            position: absolute;
            bottom: 10px;
            width: 100%;
        }
    </style>
</head>
<body>

    <h1>Graduate!!</h1>

    <div class="center-image">
        <!-- Replace 'DIRECT_IMAGE_URL' with the actual image URL -->
        <img src="users/wer.jpeg" alt="Graduate Image">
    </div>

    <footer>
        <p>2 DAYS LEFT!!!</p>
    </footer>

</body>
</html>
