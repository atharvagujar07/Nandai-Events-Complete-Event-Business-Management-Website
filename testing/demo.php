<!DOCTYPE html>
<html>
<head>
    <title>Image Gallery</title>
    <style>
        .gallery {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .gallery img {
            width: 200px;
            height: 150px;
            object-fit: cover;
            cursor: pointer;
            border-radius: 5px;
        }

        /* Fullscreen Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
        }

        .modal img {
            display: block;
            max-width: 90%;
            max-height: 90%;
            margin: 3% auto;
        }

        .close {
            position: absolute;
            top: 15px;
            right: 30px;
            color: white;
            font-size: 40px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="gallery">
    <img src="images/photo1.jpg" onclick="openImage(this.src)">
    <img src="images/photo2.jpg" onclick="openImage(this.src)">
    <img src="images/photo3.jpg" onclick="openImage(this.src)">
</div>

<!-- Fullscreen Modal -->
<div id="imageModal" class="modal">
    <span class="close" onclick="closeImage()">&times;</span>
    <img id="fullImage">
</div>

<script>
function openImage(src) {
    document.getElementById("imageModal").style.display = "block";
    document.getElementById("fullImage").src = src;
}

function closeImage() {
    document.getElementById("imageModal").style.display = "none";
}

// Close when clicking outside image
document.getElementById("imageModal").onclick = function(e) {
    if (e.target === this) {
        closeImage();
    }
}
</script>

</body>
</html>