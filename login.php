<?php
include("init.php");
if(!empty($_POST)){
    //system admin
    if(f("get_config")("sysadmin_user")
    and f("get_config")("sysadmin_password")
    and $_POST['username'] == f("get_config")("sysadmin_user") 
    and $_POST['password'] == f("get_config")("sysadmin_password")){
        $_SESSION['user'] = [
            'id'=> "sys",
            'username'=> f("get_config")("sysadmin_user"),
        ];
    }
    else{
        $q = "select * from users where 
        username=".f("str.dbq")($_POST['username']);
        $userdata = f("db.select_one")($q);
        if(empty($userdata['password']) or !password_verify($_POST['password'],$userdata['password'])){
            sleep(4);
            f("webview._layout.base")("start",['body_class'=>'bg-warning text-center pt-5','title'=>'Login']);
            ?>
            <h2>Perhatian</h2>
            User / Password salah
            <br><br>
            <a href='#' onclick='history.back()'>OK</a>
            <?php
            f("webview._layout.base")("exit"); // exit();
        }
        $_SESSION['user'] = $userdata;
    }
    f("cleartemp")();
    header("Location: index.php");
    exit();
}
f("webview._layout.base")("start",['body_class'=>'bg-warning','title'=>'Login']);
ob_start();
?>
<script>
    $( document ).ready(function() {
        if((localStorage.getItem("inputRememberPassword") ?? '') == "Y"){
            $("#inputRememberPassword").prop('checked', true);
            $("#username").val(localStorage.getItem("username"));
            $("#password").val(localStorage.getItem("password"));
        }
    });
    function rememberPassword(){
        const inputRememberPassword = $("#inputRememberPassword").is(":checked");
        if(inputRememberPassword){
            const username = $("#username").val();
            const password = $("#password").val();
            localStorage.setItem("username", username);
            localStorage.setItem("password", password);
            localStorage.setItem("inputRememberPassword", "Y");
        }
        else{
            localStorage.setItem("username", "");
            localStorage.setItem("password", "");
            localStorage.setItem("inputRememberPassword", "N");
        }
    }
</script>
<?php
$GLOBALS['page_script'] = ob_get_clean();
?>
<div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header">
                                        <h3 class="text-center font-weight-light my-4"> 
                                            <img src="assets/img/logo1.jpg" style="width: 121px;" />
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                    <form method="post">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" type="text" onchange="rememberPassword()" placeholder="username" id="username" name="username" required />
                                            <label for="username">User Name</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" type="password" onchange="rememberPassword()" placeholder="password" id="password" name="password" required />
                                            <label for="password">Password</label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" id="inputRememberPassword" onclick="rememberPassword()" type="checkbox" value="" />
                                            <label class="form-check-label" for="inputRememberPassword">Ingat User Name dan Password</label>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="forgot.php">Lupa password?</a>
                                            <input class="btn btn-primary" type="submit" value="Login"/>
                                        </div>
                                        
                                    </form>
                                        <!-- <form>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="inputEmail" type="email" placeholder="name@example.com" />
                                                <label for="inputEmail">Email address</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="inputPassword" type="password" placeholder="Password" />
                                                <label for="inputPassword">Password</label>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" id="inputRememberPassword" type="checkbox" value="" />
                                                <label class="form-check-label" for="inputRememberPassword">Remember Password</label>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <a class="small" href="password.html">Forgot Password?</a>
                                                <a class="btn btn-primary" href="index.html">Login</a>
                                            </div>
                                        </form> -->
                                    </div>
                                    <div class="card-footer text-center py-3">
                                        <div class="small"><a href="registrasi.php">Butuh akun? Daftar sekarang!</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <div id="layoutAuthentication_footer">
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; 2024</div>
                            <!-- <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div> -->
                        </div>
                    </div>
                </footer>
            </div>
        </div>
<?php
f("webview._layout.base")("end");