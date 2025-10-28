<template>
  <div class="landing-page">
    <header class="hero">
      <h1>Welcome to Our Website</h1>
      <p>Your journey to excellence starts here.</p>
      <button @click="navigateToAbout">Learn More</button>
    </header>

    <section class="features">
      <h2>Our Features</h2>
      <div class="feature" v-for="feature in features" :key="feature.id">
        <h3>{{ feature.title }}</h3>
        <p>{{ feature.description }}</p>
      </div>
    </section>

    <footer class="footer">
      <p>&copy; 2025 Our Company. All rights reserved.</p>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'LandingPage',
  data() {
    return {
      features: [
        { id: 1, title: 'Feature One', description: 'Description of feature one.' },
        { id: 2, title: 'Feature Two', description: 'Description of feature two.' },
        { id: 3, title: 'Feature Three', description: 'Description of feature three.' },
      ],
    };
  },
  methods: {
    navigateToAbout() {
      // Logic to navigate to the About page
      console.log('Navigating to About page...');
    },
  },
};
</script>

<style scoped>
.landing-page {
  font-family: Arial, sans-serif;
  text-align: center;
  padding: 20px;
}

.hero {
  background-color: #f4f4f4;
  padding: 50px 20px;
  margin-bottom: 30px;
}

.hero h1 {
  font-size: 2.5em;
  margin-bottom: 10px;
}

.hero p {
  font-size: 1.2em;
  margin-bottom: 20px;
}

.hero button {
  padding: 10px 20px;
  font-size: 1em;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.hero button:hover {
  background-color: #0056b3;
}

.features {
  margin: 20px 0;
}

.features h2 {
  font-size: 2em;
  margin-bottom: 20px;
}

.feature {
  margin-bottom: 15px;
}

.feature h3 {
  font-size: 1.5em;
  margin-bottom: 5px;
}

.footer {
  margin-top: 30px;
  font-size: 0.9em;
  color: #666;
}
</style>