<div id="profile_window" class="modal"><!--окно профиля-->
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="form" align="center">
            <h1>
                Профиль
            </h1>
            <form id="register_form">
                <input type="text" name="name" placeholder="Имя" class="form"><br>
                <input type="text" name="surname" placeholder="Фамилия" class="form"><br>
                <input type="text" name="patronymic" placeholder="Отчество" class="form"><br>
                <input type="number" name="age" placeholder="Возраст" class="form"><br>
                <input type="number" name="school" placeholder="Номер школы" class="form"><br>
                <input type="password" name="password" placeholder="Пароль" class="form"><br>
                <input type="email" name="email" placeholder="Электронная почта" class="form"><br>
                <input type="text" name="phone" placeholder="Номер телефона" class="form">
                <p>
                    <input type="submit" value="Сохранить">
                </p>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.phone').mask('8(000)000-00-00');
        $.ajax({
            type: "POST",
            url: "http://ontourapi.kvantorium33.ru/?method=user.info",
            xhrFields: {withCredentials: true},
            success: function (data) {
                data = eval("(" + data + ")");
                if (data.result == "success") {
                    createProfile(data);
                }
            }
        });
        $("#profile_window .close").click(function () {
            $("#profile_window").hide();
        });
        $("#profile_window").click(function (e) {
            if (e.target == this)
                $("#profile_window").hide();
        });
        $("#profile_form").submit(function (e) {
            $.ajax({
                type: "POST",
                url: "http://ontourapi.kvantorium33.ru/?method=user.profile",
                data: $("#profile_form").serialize(),
                xhrFields: {withCredentials: true},
                success: function (data) {
                    data = eval("(" + data + ")");
                    if (data.result == "success") {
                        $("#profile_error").html("");
                        $("#profile_window").hide();
                        $("#menu_main").show();
                        createProfile(data);
                    } else {
                        $("#profile_error").html("Неправильный логин или пароль");
                    }
                }
            });
            e.preventDefault();
        });

        $("#profile_window").click(function (e) {
            if (e.target == this)
                $("#profile_window").hide();
        });

        $("#btn_change_email").click(function () {
            $("#btn_change_email").show();
            $("#profile_window").hide();
        });

        $("#btn_change_password").click(function () {
            $("#btn_change_password").show();
            $("#profile_window").hide();
        });

        $("#btn_change_phone").click(function () {
            $("#btn_change_phone").show();
            $("#profile_window").hide();
        });
    });
</script>