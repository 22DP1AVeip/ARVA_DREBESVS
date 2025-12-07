<script setup lang="ts"> </script>

<template>
    <main>
        <section class="categories">
            <div class="category-card men">
                <div class="image-container">
                    <img src="/bildites/men_home_page.jpg" alt="Men's Clothing" />
                </div>
                <a href="/MenWear"><button>Explore Men's Wear</button></a>
            </div>

            <div class="category-card women">
                <div class="image-container">
                    <img src="/bildites/woman_home_page.jpg" alt="Women's Clothing" />
                </div>
                <a href="/WomanWear"><button>Discover Women's Style</button></a>
            </div>
        </section>
    </main>
</template>

<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f5f5f5; /* Keep this as the overall page background color */
}

main {
    text-align: center;
    padding: 50px 20px;
    background-color: white; /* Set the main section background color to white */
}

.categories {
    display: flex;
    justify-content: center;
    gap: 40px;
    margin-top: 50px;
}

.category-card {
    width: 320px;
    height: 450px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    position: relative;
}

.image-container {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    opacity: 0.5;
    transition: opacity 0.3s ease;
}

.category-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.category-card:hover .image-container {
    opacity: 1;
}

.category-card a {
    position: absolute;
    bottom: 20px;
    z-index: 2;
    text-decoration: none;
    width: 80%;
}

.category-card button {
    width: 100%;
    padding: 12px 24px;
    font-size: 16px;
    font-weight: bold;
    background-color: transparent;
    color: black;
    border: 2px solid black;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease;
    text-transform: uppercase;
}

.category-card button:hover {
    background-color: black;
    color: white;
    border: 2px solid black;
    transform: scale(1.05);
}

.category-card:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}
</style>
