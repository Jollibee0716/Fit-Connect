<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Homepage</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
  <!-- Page background image -->
  <div class="page-bg">
    <img src="{{ asset('images/hey.png') }}" alt="">
  </div>

  <div id="overlay" class="overlay" onclick="toggleMenu()"></div>
  <div class="menu-icon" onclick="toggleMenu()">☰</div>

  <ul id="menu">
    <li><a href="#home">Home</a></li>
    <li><a href="#about">About</a></li>
    <li><a href="#developers">Developers</a></li>
    <li><a href="#contact">Contact</a></li>
  </ul>

  <section id="home">
    <div class="homepage-container">
      <div class="top-part">FIT</div>
      <div class="bottom-part">NESS</div>
      <img class="background-transition" src="{{ asset('images/web4.png') }}" alt="Fitness">
      <a href="{{ route('login') }}" class="my-button">Get Started</a>
    </div>
  </section>

  <section id="about">
    <div class="content-wrapper">
      <h1>About Our Team</h1>

      <h2>Who We Are</h2>
      <p>
        We are a team of passionate <strong>Information Technology students</strong> who believe
        that the power of code can be used to transform lives. While our days are spent
        navigating complex algorithms, our goal is to build digital solutions that
        improve physical health.
      </p>

      <h2>Why This Website?</h2>
      <p>
        As IT students, we know the "Developer Lifestyle" often leads to long hours at a desk.
        We developed this fitness website to bridge the gap between technology and health,
        providing a tool that helps people like us stay active while pursuing their
        professional goals.
      </p>

      <h2>Our Vision</h2>
      <p>
       As IT students, we know the "Developer Lifestyle" often leads to long hours at a desk. We developed this fitness website to bridge the gap between technology and health, providing a tool that helps people like us stay active while pursuing their professional goals.
      </p>
    </div>
  </section>

  <section id="developers">
    <div class="developer-gallery-staggered">
      <a href="/developer/franky" class="img-upper-left"><img src="{{ asset('images/franky.jpg') }}" alt="dev1"><span class="caption">Junior Developer</span></a>
      <a href="/developer/domskie" class="img-bottom-left"><img src="{{ asset('images/dominic.jpg') }}" alt="dev2"><span class="caption">Designer/Junior Developer</span></a>
      <a href="/developer/denjie" class="img-center"><img src="{{ asset('images/denjie.jpg') }}" alt="dev3"><span class="caption">Senior Developer</span></a>
      <a href="/developer/jade" class="img-upper-right"><img src="{{ asset('images/Jade.jpg') }}" alt="dev4"><span class="caption">Junior Developer</span></a>
      <a href="/developer/mahinay" class="img-bottom-right"><img src="{{ asset('images/yandee.jpg') }}" alt="dev5"><span class="caption">Junior Developer</span></a>
    </div>
  </section>

  <section id="contact">
    <div class="content-wrapper">
      <form id="contact-form" action="https://api.web3forms.com/submit" method="POST">
         <input type="hidden" name="access_key" value="32297720-e14b-4de1-baf9-26887653330c">

        <h3>Quick Contact</h3>
        <h4>Contact us today, and get a reply within 24 hours!</h4>

        <fieldset><input name="name" placeholder="Your name" type="text" required></fieldset>
        <fieldset><input name="email" placeholder="Your Email Address" type="email" required></fieldset>
        <fieldset><input name="phone" placeholder="Your Phone Number" type="tel" required></fieldset>
        <fieldset><textarea name="message" placeholder="Type your Message Here...." required></textarea></fieldset>
        <fieldset><button name="submit" type="submit" id="contact-submit">Submit</button></fieldset>

        <input type="checkbox" name="botcheck" style="display: none;">
      </form>
    </div>
  </section>
</body>
</html>