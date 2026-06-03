<script setup lang="ts">
const handleSubscribe = () => {
    alert("Abonēts!");
};
</script>

<template>
    <footer class="footer">
        <div class="footer-content">
            <div class="social-media">
                <h3>SOCIĀLIE TĪKLI</h3>
                <div class="social-icons">
                    <img src="/bildites/Instag_Icon.png" alt="Instagram" />
                    <img src="/bildites/TikTok_Icon.png" alt="TikTok" />
                    <img src="/bildites/Faceb_Icon.png" alt="Facebook" />
                    <img src="/bildites/Youtube_Icon.png" alt="YouTube" />
                </div>
            </div>

            <div class="useful-links">
                <h3>SAITES</h3>
                <ul>
                    <li><a href="/about-us">Par mums</a></li>
                    <li><a href="/contact">Kontakti</a></li>
                    <li><a href="/privacy-policy">Privātuma politika</a></li>
                    <li><a href="/terms-of-service">Pakalpojuma noteikumi</a></li>
                </ul>
            </div>

            <div class="newsletter">
                <h3>SAŅEM JAUNĀKOS PIEDĀVĀJUMUS</h3>
                <p>Saņem jaunākās ziņas un piedāvājumus tieši savā e-pastā.</p>
                <form @submit.prevent="handleSubscribe">
                    <input type="email" placeholder="Ievadi savu e-pastu" required />
                    <button type="submit">Pierakstīties</button>
                </form>
            </div>
        </div>

        <p class="copy">&copy; 2024 AVDP. Visas tiesības aizsargātas.</p>
    </footer>
</template>

<style scoped>
.footer {
    background-color: #f9f9f9;
    padding: 20px 20px 10px;
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
}

.social-media { flex: 1; margin: 0 20px; }

.social-icons img {
    width: 30px;
    margin: 0 8px;
    transition: transform 0.3s ease;
}
.social-icons img:hover { transform: scale(1.2); }

.useful-links { flex: 1; margin: 0 20px; text-align: left; }
.useful-links h3 { font-size: 14px; font-weight: bold; margin-bottom: 8px; }
.useful-links ul { list-style: none; padding: 0; }
.useful-links li { margin: 4px 0; }
.useful-links a { text-decoration: none; color: #333; font-size: 13px; transition: color 0.3s; }
.useful-links a:hover { color: #e74c3c; }

.newsletter { flex: 1; margin: 0 20px; text-align: left; }
.newsletter h3 { font-size: 14px; font-weight: bold; margin-bottom: 6px; }
.newsletter p { font-size: 13px; margin-bottom: 8px; color: #666; }
.newsletter form { display: flex; gap: 8px; }
.newsletter input { padding: 8px; border: 1px solid #ddd; border-radius: 5px; width: 200px; font-size: 13px; }
.newsletter button {
    padding: 8px 12px; border: none; background-color: #e74c3c;
    color: white; border-radius: 5px; cursor: pointer; font-size: 13px;
}
.newsletter button:hover { background-color: #c0392b; }

.copy { font-size: 12px; margin-top: 10px; color: #777; }
</style>
