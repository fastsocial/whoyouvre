<!doctype html>
<html>
    <head>
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" type="text/javascript"></script>
        <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js" type="text/javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.2.1/raphael.min.js" type="text/javascript"></script>
        <script src="/js/script.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="/css/style.css">
    </head>
    <body>
        <h1>Whouyvre!</h1>
        <form method="post" enctype="multipart/form-data" class="whoyouvre-admin-main-form-element">
            <div>
                Выберите картину для загруки: <input type="file" name="image"></input><br>
                Или укажите URL: <input type="text" name="file-url" value="" placeholder="http://google.com/icon.png">
            </div>
            <div class="sumbit">
                <input type="submit" name="load" value="загрузить картину">
            </div>
        </form>
    </body>
</html>