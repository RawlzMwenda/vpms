<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>VPMS Landing Page</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex flex-col min-h-screen">

  <!-- Header -->
  <header class="bg-white shadow">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
      <h1 class="text-2xl font-bold text-indigo-600">VPMS</h1>
      <nav class="space-x-4">
        <a href="#about" class="text-gray-600 hover:text-indigo-600">About</a>
        <a href="#features" class="text-gray-600 hover:text-indigo-600">Features</a>
        <a href="#testimonials" class="text-gray-600 hover:text-indigo-600">Testimonials</a>
        <a href="#contact" class="text-gray-600 hover:text-indigo-600">Contact</a>
      </nav>
    </div>
  </header>
<!-- Hero -->
<section class="bg-gray-50 py-20" id="hero">
  <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">
    
    <!-- Text Content -->
    <div class="md:w-1/2 md:pr-12 text-center md:text-left mb-10 md:mb-0">
      <h2 class="text-4xl font-bold text-gray-800 mb-4">Revolutionize Your Parking</h2>
      <p class="text-gray-600 mb-6">
        VPMS uses real-time data and automation to streamline parking like never before.
      </p>
      <br><br><br><br>
      <a href="users/login.php" class="bg-indigo-600 text-white px-6 py-3 rounded hover:bg-indigo-500 transition">
        Index
      </a>
      
      <a href="admin/index.php" class="bg-indigo-600 text-white px-6 py-3 rounded hover:bg-indigo-500 transition">
        Admin
      </a>
    </div>

    <!-- Image -->
    <div class="md:w-1/2">
      <img src="users/images/parking-concept.jpg" alt="VPMS Illustration"
        class="w-full max-w-md mx-auto rounded shadow-xl" />
    </div>

  </div>
</section>

<!-- About Section -->
<!-- About Section -->
<section class="py-16 bg-white" id="about">
  <div class="container mx-auto px-4 grid md:grid-cols-2 gap-8 items-center">
    
    <!-- Image on the left -->
    <div class="flex justify-center">
      <img src="users/images/using-parking.jpg" 
           alt="About VPMS" 
           class="rounded-lg shadow-2xl max-w-md w-full">
    </div>

    <!-- Text on the right -->
    <div>
      <h3 class="text-3xl font-bold text-gray-800 mb-4">About VPMS</h3>
      <p class="text-gray-600 mb-4">
        VPMS is a modern parking solution designed to reduce time spent searching for parking spots. Using intelligent sensors and real-time data, we help users find available spaces effortlessly.
      </p>
      <p class="text-gray-600">
        Whether you're in a busy city or a small town, VPMS simplifies your parking experience through mobile app access, predictive analytics, and secure payments.
      </p>
    </div>

  </div>
</section>



<!-- Features Section -->
<section class="py-16 bg-gray-50" id="features">
  <div class="container mx-auto px-4">
    <h3 class="text-3xl font-bold text-center text-gray-800 mb-10">Key Features</h3>
    <div class="grid md:grid-cols-3 gap-8">
      
      <div class="p-6 border rounded-lg text-center bg-white">
        <img src="users/images/time.jpg" alt="Real-Time Availability" 
             class="w-full rounded shadow-lg h-40 object-cover mb-4">
        <h4 class="text-xl font-semibold mb-2">Real-Time Availability</h4>
        <p class="text-gray-600">Know exactly where to park before you arrive.</p>
      </div>

      <div class="p-6 border rounded-lg text-center bg-white">
        <img src="users/images/mobile-access.jpg" alt="Mobile App" 
             class="w-full rounded shadow-lg h-40 object-cover mb-4">
        <h4 class="text-xl font-semibold mb-2">Mobile App Access</h4>
        <p class="text-gray-600">Reserve and pay directly from your phone.</p>
      </div>

      <div class="p-6 border rounded-lg text-center bg-white">
        <img src="users/images/online-payment.jpg" alt="Payments" 
             class="w-full rounded shadow-lg h-40 object-cover mb-4">
        <h4 class="text-xl font-semibold mb-2">Seamless Payments</h4>
        <p class="text-gray-600">Multiple payment options with instant confirmation.</p>
      </div>

    </div>
  </div>
</section>



  <!-- Testimonials Section -->
  <section class="py-16 bg-white" id="testimonials">
    <div class="container mx-auto px-4">
      <h3 class="text-3xl font-bold text-center text-gray-800 mb-10">What Our Users Say</h3>
      <div class="grid md:grid-cols-3 gap-8">
        <div class="bg-gray-100 p-6 rounded-lg shadow">
          <p class="text-gray-700 italic mb-4">“VPMS has saved me so much time. I no longer drive in circles looking for parking!”</p>
          <p class="text-indigo-600 font-semibold">— Jamie R.</p>
        </div>
        <div class="bg-gray-100 p-6 rounded-lg shadow">
          <p class="text-gray-700 italic mb-4">“The app is super easy to use, and payment is a breeze. Highly recommend it!”</p>
          <p class="text-indigo-600 font-semibold">— Priya S.</p>
        </div>
        <div class="bg-gray-100 p-6 rounded-lg shadow">
          <p class="text-gray-700 italic mb-4">“Excellent customer service and the tech just works. Love it!”</p>
          <p class="text-indigo-600 font-semibold">— Carlos M.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section class="py-16 bg-gray-50" id="contact">
    <div class="container mx-auto px-4 max-w-2xl">
      <h3 class="text-3xl font-bold text-center text-gray-800 mb-6">Contact Us</h3>
      <form class="bg-white p-6 rounded shadow space-y-4">
        <input type="text" placeholder="Your Name" class="w-full p-3 border rounded">
        <input type="email" placeholder="Your Email" class="w-full p-3 border rounded">
        <textarea placeholder="Your Message" class="w-full p-3 border rounded h-32"></textarea>
        <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded hover:bg-indigo-500">Send Message</button>
      </form>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-800 text-white text-center py-6">
    <p>&copy; 2025 VPMS. All rights reserved.</p>
  </footer>

  <!-- Smooth Scrolling -->
  <script>
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
          behavior: 'smooth'
        });
      });
    });
  </script>

</body>
</html>
