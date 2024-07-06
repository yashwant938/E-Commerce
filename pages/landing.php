<?php
if(!defined('app')) {
   die('Direct access not permitted');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyb3rC1ph3r Login</title>
  <link rel="stylesheet" href="files/css/landing.css">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <style>
    .errormsg{
        color:red;
        text-align: center;
        width:100%;
        height:40px;
        line-height: 40px;
        font-size:16px;
    }
  </style>
</head>
<body>
   <div class="container">
    <div class="row1">
       <main>
        <img src="files/img/slide3.png" class="slide" alt="">
        <img src="files/img/slide4.png" class="slide" alt="">
        <img src="files/img/longin.jpg" class="slide" alt="">
       </main>
    </div>
    <div class="row2">
        <img class="image" src="files/img/longin.jpg" alt="Description of your image">
            <div class="card">
                <form id="login-form" method="POST" action="<?php echo $route->urlFor('signin'); ?>">
                <?php if(isset($_SESSION["error_message"])){ ?> 
            <div class="errormsg">
                <?php echo $_SESSION["error_message"]; unset($_SESSION["error_message"]); ?>
            </div>        
        <?php } ?>
                    <input type="text" name="email" id="email" placeholder="Email">
                    <input type="password" name="password" id="password" placeholder="Password">
                    <?=$csrf->input('login-form', -1, 1);?>
                    <div class="g-recaptcha" data-sitekey="<?php echo Config::CAPTCHA_SITE; ?>"></div>

                    <div class="buttons">
                        <button type="submit" class="login-button">Login</button>
                    </div>
                        <a href="<?php echo $route->urlFor('signUpForm'); ?>" class="register-button">Sign Up</a>
                    <div class="forgot-pass"><a href="<?php echo $route->urlFor('resetPassword'); ?>">Forgotten password?</a></div>
                </form>
            </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="files/js/jquery.validate.min.js"></script>

<script>
    $("#login-form").validate({
        rules: {
    password: "required",
    email: {
      required: true,
      email: true
    }
  },

  submitHandler: function(form) {
    form.submit();
  }
 });

    const slides = document.querySelectorAll(".slide");
var counter = 0;
var numberOfSlides=slides.length

slides.forEach((slide, index) => {
    slide.style.top = `${index * 100}%`;
});

const goNext = () => {
    if (counter == slides.length - 1) {
        counter = 0;
        slideImage();
    } else {
        counter++;
        slideImage();
    }
}

const goPrev = () => {
    if (counter == 0) {
        counter = slides.length - 1;
        slideImage();
    } else {
        counter--;
        slideImage();
    }
}



const slideImage = () => {
    slides.forEach((slide) => {
        console.log(counter)
        slide.style.transform = `translateY(-${(counter) * 100}%)`;
    });
};


setInterval(goNext(),1000);


</script>
</body>
</html>
