<?php
    // - Test pour ne pas permettre l'accés direct à cette page
    if (!isset($_commande)) {
        header("Location: ../index.php");
        die();
    }
?>

        </main>
        <footer>
            <p>&copy; Copyright <?= date("Y") ?> - Lamsahle El Mamoun</p>
        </footer>
    </body>
</html>