<script setup lang="ts"></script>

<template>
    <main class="home">
        <section class="categories">
            <article class="category-card men">
                <div class="card-media">
                    <img src="/bildites/men_home_page.jpg" alt="Vīriešu apģērbs" />
                    <div class="media-overlay"></div>
                </div>
                <div class="card-content">
                    <h2>Vīriešu apģērbs</h2>
                    <p>Asas līnijas, dziļi toņi un pārliecība katrā detaļā.</p>
                    <a class="cta" href="/MenWear">Apskatīt vīriešu apģērbu</a>
                </div>
            </article>

            <article class="category-card women">
                <div class="card-media">
                    <img src="/bildites/woman_home_page.jpg" alt="Sieviešu apģērbs" />
                    <div class="media-overlay"></div>
                </div>
                <div class="card-content">
                    <h2>Sieviešu apģērbs</h2>
                    <p>Maigi akcenti, plūstošas formas un mūsdienīga elegance.</p>
                    <a class="cta" href="/WomanWear">Apskatīt sieviešu apģērbu</a>
                </div>
            </article>
        </section>
    </main>
</template>

<style scoped>
:root {
    color-scheme: light;
}

.home {
    --ink: #072536;
    --ink-soft: #0f3a4c;
    --mist: #f7fbfc;
    --glow: #cfe3ea;
    --accent: #7bb3c6;
    min-height: 100vh;
    padding: 70px 24px 90px;
    text-align: center;
    background:
        radial-gradient(1200px 600px at 15% 10%, #ffffff 0%, rgba(247, 251, 252, 0.6) 55%, transparent 70%),
        radial-gradient(900px 500px at 85% 20%, rgba(199, 223, 233, 0.55) 0%, transparent 65%),
        linear-gradient(180deg, #ffffff 0%, var(--mist) 100%);
    color: var(--ink);
}

.categories {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 32px;
    max-width: 980px;
    margin: 0 auto;
}

.category-card {
    position: relative;
    border-radius: 22px;
    overflow: hidden;
    background: #ffffff;
    box-shadow: 0 18px 40px rgba(7, 37, 54, 0.12);
    display: flex;
    flex-direction: column;
    min-height: 460px;
    transition: transform 0.35s ease, box-shadow 0.35s ease;
    animation: fade-up 0.8s ease-out both;
}

.category-card:nth-child(2) {
    animation-delay: 0.1s;
}

.card-media {
    position: relative;
    flex: 1;
    min-height: 280px;
    overflow: hidden;
}

.card-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transform: scale(1.02);
    transition: transform 0.45s ease;
}

.media-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(7, 37, 54, 0.05) 0%, rgba(7, 37, 54, 0.65) 100%);
    opacity: 0.85;
    transition: opacity 0.35s ease;
}

.card-content {
    padding: 24px 26px 30px;
    text-align: left;
}

.card-content h2 {
    font-size: 26px;
    margin-bottom: 10px;
}

.card-content p {
    font-size: 14px;
    color: rgba(7, 37, 54, 0.7);
    margin-bottom: 22px;
}

.cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 20px;
    border-radius: 999px;
    border: 1px solid rgba(7, 37, 54, 0.2);
    background: #ffffff;
    color: var(--ink);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-size: 12px;
    text-decoration: none;
    transition: background 0.3s ease, color 0.3s ease, border 0.3s ease, transform 0.3s ease;
}

.category-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 24px 60px rgba(7, 37, 54, 0.18);
}

.category-card:hover .card-media img {
    transform: scale(1.08);
}

.category-card:hover .media-overlay {
    opacity: 0.55;
}

.category-card:hover .cta {
    background: var(--ink);
    color: #ffffff;
    border-color: var(--ink);
    transform: translateY(-2px);
}

@keyframes fade-up {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 720px) {
    .home {
        padding: 50px 18px 70px;
    }

    .card-content {
        text-align: center;
    }
}
</style>
