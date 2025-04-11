<?php include 'header.php'; ?>
<style>
    /* Ensure the carousel images are aligned horizontally */
#carousel {
  display: flex;
  width: 100%;
  height: 400px; /* Set fixed height for the carousel */
 
}

/* Style the images inside the carousel */
#carousel img {
  width: 100%;
  height: 100%; /* Make images fill the container */
  object-fit: cover; /* Ensures images fill the container without distorting the aspect ratio */
  flex-shrink: 0; /* Prevent images from shrinking */
  transition: transform 0.5s ease; /* Smooth transition when moving */
}

</style>

<!-- Carousel -->
<section class="relative">
  <div class="overflow-hidden">
    <div class="flex transition-transform duration-500" id="carousel">
      <img src="img/img1.jpg" alt="Slide 1" class="w-full">
      <img src="img/img2.jpg" alt="Slide 2" class="w-full">
      <img src="img/img3.jpg" alt="Slide 3" class="w-full">
    </div>
  </div>
  <button id="prev" class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-black bg-opacity-50 text-white px-3 py-2 rounded-full">
    &#8249;
  </button>
  <button id="next" class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-black bg-opacity-50 text-white px-3 py-2 rounded-full">
    &#8250;
  </button>
</section>

<!-- Products Section -->
<section id="products" class="py-16 bg-gray-100">
  <div class="container mx-auto text-center">
    <h2 class="text-3xl font-bold mb-8">Our Products</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <?php
      // Fetch products from the database
      $sql = "SELECT id, name, price, stock, image_path FROM products WHERE stock > 0 "; // Include image_path in the query
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
          // Loop through each product and display it
          while ($row = $result->fetch_assoc()) {
              echo '<div class="group bg-white shadow-md rounded-lg p-6 overflow-hidden">';

              // Clean up the image path and remove '../' if present
              $image = !empty($row['image_path']) ? str_replace('../', '', $row['image_path']) : 'https://via.placeholder.com/150';

              // Product image with hover zoom effect
              echo '<div class="overflow-hidden rounded-lg">';
              echo '<img src="' . htmlspecialchars($image) . '" alt="' . htmlspecialchars($row['name']) . '" class="w-full h-48 object-cover transform transition-transform duration-300 group-hover:scale-110">';
              echo '</div>';

              // Display product details
              echo '<h3 class="text-xl font-semibold mt-4">' . htmlspecialchars($row['name']) . '</h3>';
              echo '<p class="text-gray-600">Price: ₱' . number_format($row['price'], 2) . '</p>';

              // Learn More button
              echo '<a href="products.php" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 inline-block">Learn More</a>';
              echo '</div>';
          }
      } else {
          echo '<p class="text-center text-gray-600">No products available.</p>';
      }
      $conn->close();
      ?>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-gray-800 text-white py-8">
  <div class="container mx-auto text-center">
    <p>&copy; 2024 Brand. All rights reserved.</p>
  </div>
</footer>

<script>
// Carousel logic
const carousel = document.getElementById('carousel');
const prevBtn = document.getElementById('prev');
const nextBtn = document.getElementById('next');

let currentSlide = 0;
const totalSlides = 3; // Total number of slides
const slideInterval = 3000; // Time interval in milliseconds

// Function to update the carousel position
function updateCarousel() {
  carousel.style.transform = `translateX(-${currentSlide * 100}%)`;
}

// Manual navigation
nextBtn.addEventListener('click', () => {
  currentSlide = (currentSlide + 1) % totalSlides;
  updateCarousel();
  resetAutoSlide(); // Reset the auto-slide timer when manually navigating
});

prevBtn.addEventListener('click', () => {
  currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
  updateCarousel();
  resetAutoSlide(); // Reset the auto-slide timer when manually navigating
});

// Auto-slide functionality
let autoSlide = setInterval(() => {
  currentSlide = (currentSlide + 1) % totalSlides;
  updateCarousel();
}, slideInterval);

// Reset auto-slide timer
function resetAutoSlide() {
  clearInterval(autoSlide);
  autoSlide = setInterval(() => {
    currentSlide = (currentSlide + 1) % totalSlides;
    updateCarousel();
  }, slideInterval);
}
</script>

</body>
</html>
