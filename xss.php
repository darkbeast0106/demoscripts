<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xss demo</title>
</head>

<body>
    <?php if (isset($_POST['input']) && !empty($_POST['input'])) : ?>
        <p>A beírt szöveg: <?php echo $_POST['input'] ?></p>
    <?php endif; ?>

    <form method="post">
        <textarea name="input" id="input" cols="30" rows="10"></textarea><br>
        <button type="submit">Küld</button>
    </form>
</body>

</html>

<?php 
/*
pl.: 
<script>
    alert('sajt');
</script>
*/
?>