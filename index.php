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
            flex-direction: column;
            justify-content: space-between;
        }

        /* Header animation */
        h1 {
            position: absolute;
            top: 20px;
            left: 100%;
            font-size: 3rem;
            color: white;
            animation: moveLeft 10s linear infinite;
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
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
        }

        .center-image img {
            max-width: 100%;
            max-height: 80%;
        }

        /* Footer Caption */
        footer {
            text-align: center;
            color: white;
            font-size: 1.5rem;
            padding: 10px;
        }
    </style>
</head>
<body>

    <h1>Graduate!!</h1>

    <div class="center-image">
        <!-- Replace 'DIRECT_IMAGE_URL' with the actual image URL -->
        <img src="" alt="Graduate Image">
    </div>

    <footer>
        <p>Meow Meow Meow Meow Meow Meow Meow Meow Meow Meow Meow Meow </p>
    </footer>

    <!-- Background music -->
    <audio autoplay loop>
        <source src="sounds/fx.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>

</body>
</html>
