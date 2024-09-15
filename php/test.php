<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rappel de Vote</title>
    <style>
        /* Style de la fenêtre modale */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .close {
            color: red;
            float: right;
            font-size: 28px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<!-- Fenêtre modale -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>N'oubliez pas d'aller voter ! <a href="https://top-serveurs.net/arma3/vote/fr-w40k-le-639th-regiment-cadian" target="_blank">Cliquez ici pour voter</a></p>
    </div>
</div>

<script>
    // Fonction pour afficher la modale
    function showModal() {
        var modal = document.getElementById("myModal");
        var closeBtn = document.getElementsByClassName("close")[0];

        modal.style.display = "flex";

        closeBtn.onclick = function() {
            modal.style.display = "none";
            localStorage.setItem('lastShown', Date.now()); // Stocker le temps de l'affichage
        };

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
                localStorage.setItem('lastShown', Date.now()); // Stocker le temps de l'affichage
            }
        };
    }

    // Vérifier si la modale doit être affichée toutes les 3 heures
    function checkModal() {
        var lastShown = localStorage.getItem('lastShown');
        var now = Date.now();

        // Si la modale n'a jamais été affichée ou si 3 heures sont écoulées
        if (!lastShown || (now - lastShown) >= 5000) {
            showModal();
        }
    }

    // Appeler la vérification de la modale au chargement de la page
    window.onload = function() {
        checkModal();

        // Répéter toutes les 3 heures
        setInterval(checkModal, 5000);
    };
</script>

</body>
</html>
