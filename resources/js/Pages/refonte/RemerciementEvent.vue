<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { BContainer, BRow, BCol, BButton } from 'bootstrap-vue-next'
import EventLayout from '@/Layouts/EventLayout.vue'

defineOptions({
  layout: EventLayout
})

// Props pour les données d'inscription (optionnel)
defineProps<{
  registration?: {
    name: string
    email: string
  }
}>()
</script>

<template>
  <div>
    <Head title="Inscription Réussie - EtapSup" />

    <!-- Hero Success Section -->
    <section class="success-hero">
      <BContainer>
        <BRow class="justify-content-center text-center">
          <BCol lg="8" xl="6">
            <div class="success-icon mb-4">
              <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="hero-title mb-4">
              Félicitations ! 🎉
            </h1>
            <h2 class="hero-subtitle mb-4">
              Votre inscription au webinaire a été confirmée
            </h2>
            <p class="lead mb-5">
              Vous êtes maintenant inscrit(e) à notre webinaire exclusif sur les études à l'étranger.
              Un email de confirmation vous a été envoyé.
            </p>
          </BCol>
        </BRow>
      </BContainer>
    </section>

    <!-- Instructions Section -->
    <section class="instructions-section">
      <BContainer>
        <BRow class="justify-content-center">
          <BCol lg="10" xl="8">
            <div class="instructions-card">
              <h3 class="section-title mb-4 text-center">
                Prochaines étapes importantes
              </h3>

              <div class="instruction-steps">
                <div class="step-item">
                  <div class="step-icon">
                    <i class="fas fa-envelope"></i>
                  </div>
                  <div class="step-content">
                    <h4>1. Consultez votre adresse mail</h4>
                    <p>Un email de confirmation avec les détails du webinaire vous a été envoyé à l'instant.</p>
                  </div>
                </div>

                <div class="step-item">
                  <div class="step-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                  </div>
                  <div class="step-content">
                    <h4>2. Vérifiez vos spams</h4>
                    <p>Si vous ne trouvez pas notre email, pensez à vérifier votre dossier spam ou courrier indésirable.</p>
                  </div>
                </div>

                <div class="step-item">
                  <div class="step-icon">
                    <i class="fas fa-calendar-check"></i>
                  </div>
                  <div class="step-content">
                    <h4>3. Notez la date dans votre agenda</h4>
                    <p>Le webinaire aura lieu <strong>Jeudi 11 Octobre à 19h (GMT+1)</strong>. Ne manquez pas cette opportunité unique !</p>
                  </div>
                </div>
              </div>
            </div>
          </BCol>
        </BRow>
      </BContainer>
    </section>

    <!-- Additional Info Section -->
    <section class="info-section">
      <BContainer>
        <BRow class="justify-content-center text-center">
          <BCol lg="8">
            <div class="info-card">
              <h3 class="mb-3">En attendant le webinaire...</h3>
              <p class="mb-4">
                Rejoignez notre communauté d'étudiants qui ont réalisé leur rêve d'étudier à l'étranger.
              </p>

              <!-- Lien de connexion masqué comme demandé -->
              <div class="auth-section" style="display: none;">
                <BButton
                  variant="outline-primary"
                  size="lg"
                  :href="route('login')"
                  class="auth-cta-button"
                >
                  Se connecter à votre espace
                </BButton>
              </div>
            </div>
          </BCol>
        </BRow>
      </BContainer>
    </section>
  </div>
</template>

<style scoped>
/* Success Hero Section */
.success-hero {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 6rem 0 4rem;
  text-align: center;
}

.success-icon {
  font-size: 4rem;
  color: #2dd4bf;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); }
}

.hero-title {
  font-size: 3rem;
  font-weight: 800;
  color: white;
  margin-bottom: 1.5rem;
}

.hero-subtitle {
  font-size: 1.5rem;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.9);
  margin-bottom: 1.5rem;
}

.lead {
  font-size: 1.125rem;
  color: rgba(255, 255, 255, 0.8);
  line-height: 1.6;
}

/* Instructions Section */
.instructions-section {
  padding: 5rem 0;
  background: #f8fafc;
}

.instructions-card {
  background: white;
  padding: 3rem;
  border-radius: 1rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.section-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 2rem;
}

.instruction-steps {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.step-item {
  display: flex;
  align-items: flex-start;
  gap: 1.5rem;
  padding: 1.5rem;
  background: #f1f5f9;
  border-radius: 0.75rem;
  transition: transform 0.2s ease;
}

.step-item:hover {
  transform: translateY(-2px);
}

.step-icon {
  flex-shrink: 0;
  width: 3rem;
  height: 3rem;
  background: linear-gradient(45deg, #ed2939, #cc1f2d);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
}

.step-content h4 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.step-content p {
  color: #6b7280;
  line-height: 1.6;
  margin: 0;
}

/* Info Section */
.info-section {
  padding: 4rem 0;
  background: white;
}

.info-card {
  padding: 2rem;
}

.info-card h3 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1f2937;
}

.info-card p {
  color: #6b7280;
  font-size: 1.125rem;
  line-height: 1.6;
}

.auth-cta-button {
  background: linear-gradient(45deg, #ed2939, #cc1f2d);
  border: none;
  padding: 1rem 2rem;
  font-weight: 600;
  border-radius: 0.5rem;
  transition: all 0.3s ease;
  color: white !important;
}

.auth-cta-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(237, 41, 57, 0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
  .success-hero {
    padding: 4rem 0 3rem;
  }

  .hero-title {
    font-size: 2rem;
  }

  .hero-subtitle {
    font-size: 1.25rem;
  }

  .instructions-card {
    padding: 2rem 1.5rem;
  }

  .step-item {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }

  .step-icon {
    align-self: center;
  }
}
</style>
