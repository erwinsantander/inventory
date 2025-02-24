<?php
// Simple PHP script to serve the HTML page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erwin Myaot</title>
    <style>
        body {
            margin: 0;
            overflow: hidden;
            background: url('konoha.gif') no-repeat center center fixed;
            background-size: cover;
        }
        .whale {
            position: absolute;
            width: 150px; /* Increased size */
            height: auto;
            transition: transform 0.1s linear;
        }
    </style>
</head>
<body>
    <img src="sasuke.gif" alt="Whale" class="whale" id="whale">
    <audio id="waterSound" loop>
        <source src="naruto.mp3" type="audio/mp3">
        Your browser does not support the audio element.
    </audio>
    
    <script>
        let whale = document.getElementById("whale");
        let mouseX = 0, mouseY = 0;
        let whaleX = 0, whaleY = 0;
        let waterSound = document.getElementById("waterSound");

        document.addEventListener("mousemove", (event) => {
            mouseX = event.clientX;
            mouseY = event.clientY;
            if (waterSound.paused) {
                waterSound.play();
            }
        });

        function moveWhale() {
            whaleX += (mouseX - whaleX) * 0.1;
            whaleY += (mouseY - whaleY) * 0.1;
            whale.style.transform = `translate(${whaleX - 100}px, ${whaleY - 60}px)`;
            requestAnimationFrame(moveWhale);
        }

        moveWhale();
    </script>
</body>
</html>
