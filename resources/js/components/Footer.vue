<script setup lang="ts">
// handleSubscribe function to prevent errors
const handleSubscribe = () => {
    alert("Subscribed!");
};
</script>


<template>
    <footer class="footer">
        <div class="footer-content">
            <div class="social-media">
                <h3>SOCIAL MEDIA</h3>
                <div class="social-icons">
                    <img src="/bildites/Instag_Icon.png" alt="Instagram" />
                    <img src="/bildites/TikTok_Icon.png" alt="TikTok" />
                    <img src="/bildites/Faceb_Icon.png" alt="Facebook" />
                    <img src="/bildites/Youtube_Icon.png" alt="YouTube" />
                </div>
            </div>

            <div class="useful-links">
                <h3>LINKS</h3>
                <ul>
                    <li><a href="/about-us">About Us</a></li>
                    <li><a href="/contact">Contact</a></li>
                    <li><a href="/privacy-policy">Privacy Policy</a></li>
                    <li><a href="/terms-of-service">Terms of Service</a></li>
                </ul>
            </div>

            <div class="newsletter">
                <h3>GET THE LATEST DEALS</h3>
                <p>Get the latest updates and offers delivered straight to your inbox.</p>
                <form @submit.prevent="handleSubscribe">
                    <input type="email" placeholder="Enter your email" required />
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>

        <p>&copy; 2024 AVDP. All rights reserved.</p>
    </footer>
</template>

<style scoped>
.footer {
    background-color: #f9f9f9;
    padding: 40px 20px;
    text-align: center;
    border-top: 2px solid #bdc3c7;
    font-family: "Poppins", sans-serif;
}

.footer-content {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    max-width: 1200px;
    margin: 0 auto;
    font-family: "Poppins", sans-serif;
}

.social-media {
    flex: 1;
    margin: 0 20px;
}

.social-icons img {
    width: 40px;
    margin: 0 15px;
    transition: transform 0.3s ease, filter 0.3s ease;
}

.social-icons img:hover {
    transform: scale(1.2);
    filter: brightness(1.2);
}

.useful-links {
    flex: 1;
    margin: 0 20px;
    text-align: left;
}

.useful-links h3 {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 15px;
}

.useful-links ul {
    list-style: none;
    padding: 0;
}

.useful-links li {
    margin: 8px 0;
}

.useful-links a {
    text-decoration: none;
    color: #333;
    font-size: 14px;
    transition: color 0.3s ease;
}

.useful-links a:hover {
    color: #e74c3c;
}

.newsletter {
    flex: 1;
    margin: 0 20px;
    text-align: left;
}

.newsletter h3 {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
}

.newsletter p {
    font-size: 14px;
    margin-bottom: 15px;
    color: #666;
}

.newsletter form {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.newsletter input {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: 250px;
    font-size: 14px;
}

.newsletter button {
    padding: 10px 15px;
    border: none;
    background-color: #e74c3c;
    color: white;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s ease;
}

.newsletter button:hover {
    background-color: #c0392b;
}

p {
    font-size: 14px;
    margin-top: 20px;
    color: #777;
}
</style>
