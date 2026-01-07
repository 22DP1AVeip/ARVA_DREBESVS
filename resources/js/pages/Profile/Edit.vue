<script setup lang="ts">
import NavBar from "../../components/NavBar.vue";
import Footer from "../../components/NavFooter.vue";
import { useForm, usePage } from "@inertiajs/vue3";

const page = usePage<any>();
const user = page.props.user;

const profileForm = useForm({
  name: user.name,
  email: user.email,
});

const passwordForm = useForm({
  current_password: "",
  password: "",
  password_confirmation: "",
});

function updateProfile() {
  profileForm.patch("/profile");
}

function updatePassword() {
  passwordForm.put("/profile/password");
}
</script>

<template>
  <main>
    <NavBar />

    <div class="profile-wrap">
      <h1>Profila iestatījumi</h1>

      <!-- PROFILE -->
      <section class="card">
        <h2>Profils</h2>

        <label>Vārds</label>
        <input v-model="profileForm.name" />

        <label>E-pasts</label>
        <input v-model="profileForm.email" />

        <button @click="updateProfile" :disabled="profileForm.processing">
          Saglabāt
        </button>

        <p v-if="profileForm.recentlySuccessful" class="success">
          Profils saglabāts
        </p>
      </section>

      <!-- PASSWORD -->
      <section class="card">
        <h2>Mainīt paroli</h2>

        <label>Pašreizējā parole</label>
        <input type="password" v-model="passwordForm.current_password" />

        <label>Jaunā parole</label>
        <input type="password" v-model="passwordForm.password" />

        <label>Apstiprināt paroli</label>
        <input type="password" v-model="passwordForm.password_confirmation" />

        <button @click="updatePassword" :disabled="passwordForm.processing">
          Mainīt paroli
        </button>

        <p v-if="passwordForm.recentlySuccessful" class="success">
          Parole nomainīta
        </p>
      </section>

      <!-- PLACEHOLDER FAVORITES -->
      <section class="card muted">
        <h2>Favorīti (drīzumā)</h2>
        <p>Šeit drīzumā būs tavi favorīti.</p>
      </section>
    </div>

    <Footer />
  </main>
</template>

<style scoped>
.profile-wrap {
  max-width: 700px;
  margin: 0 auto;
  padding: 24px;
}

h1 {
  font-size: 28px;
  font-weight: 900;
  margin-bottom: 20px;
}

.card {
  background: #fff;
  border: 1px solid #eee;
  border-radius: 14px;
  padding: 18px;
  margin-bottom: 16px;
}

.card h2 {
  margin-bottom: 12px;
}

label {
  font-weight: 700;
  display: block;
  margin-top: 10px;
}

input {
  width: 100%;
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #ccc;
}

button {
  margin-top: 14px;
  background: #111;
  color: #fff;
  padding: 10px 14px;
  border-radius: 10px;
  font-weight: 800;
  cursor: pointer;
}

.success {
  margin-top: 10px;
  color: green;
  font-weight: 700;
}

.muted {
  opacity: 0.6;
}
</style>
