<?php
session_start();
session_destroy();
header('Location: ../../pages/public/login.php?msg=' . urlencode('Você foi desconectado com sucesso.'));
exit;
?>
