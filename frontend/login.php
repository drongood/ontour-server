<div id="login_window" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="form" align="center">
            <h1>
                Вход ON TOUR
            </h1>
            <form id="login_form">
                <h3>
                    Имя
                </h3>
                <input type="text" name="login">
                <h3>
                    Пароль
                </h3>
                <input type="password" name="password">
                <h3>
                </h3>
                <div id="login_error"></div>
                <input type="submit">
            </form>
            <h3>
                Если у вас нет аккаунта, Вы можете <a href=# id="btn_register">зарегистрироваться</a>
            </h3>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#btn_login").click(function () {
            $("#login_window").show();
        });

        $("#login_window .close").click(function () {
            $("#login_window").hide();
        });

        $("#login_window").click(function (e) {
            if(e.target == this)
                $("#login_window").hide();
        });

        $("#login_form").submit(function(e) {

            $.ajax({
                type: "POST",
                url: "http://ontourapi.kvantorium33.ru/?method=user.login",
                data: $("#login_form").serialize(),
                success: function(data)
                {
                    data = eval("(" + data + ")");
                    if(data.result == "success") {
                        $("#login_error").html("");
                        $("#login_window").hide();
                        $("#menu_login").hide();
                        $("#menu_logout").show();
                        $("#btn_profile").html("Профиль");
                        $("#btn_profile").attr("title", "Click here!");
                    } else {
                        $("#login_error").html("Неправильный логин или пароль");
                    }
                }
            });
            e.preventDefault();
        });
    });
</script>