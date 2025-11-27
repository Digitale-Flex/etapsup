<script setup lang="ts">
// Refonte: Story 1.1.1 - Page Événementielle Landing Page inspired by Diplomeo.com
import { ref, reactive, onMounted, watch } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { BButton, BFormInput, BFormGroup, BFormSelect, BFormInvalidFeedback, BContainer, BRow, BCol } from 'bootstrap-vue-next'

// Utiliser un layout personnalisé sans le footer yodinvest
import EventLayout from '@/Layouts/EventLayout.vue'

defineOptions({
  layout: EventLayout
})

// Meta Props
defineProps<{
  event?: {
    title: string
    date: string
    description: string
    benefits: string[]
    testimonials: any[]
  }
}>()

// Countries list for the form (Including RDC as requested)
const countries = ref([
  { value: '', text: 'Sélectionnez votre pays' },
  { value: 'CD', text: '🇨🇩 République Démocratique du Congo (RDC)' },
  { value: 'CM', text: '🇨🇲 Cameroun' },
  { value: 'SN', text: '🇸🇳 Sénégal' },
  { value: 'CI', text: '🇨🇮 Côte d\'Ivoire' },
  { value: 'MA', text: '🇲🇦 Maroc' },
  { value: 'TN', text: '🇹🇳 Tunisie' },
  { value: 'DZ', text: '🇩🇿 Algérie' },
  { value: 'NG', text: '🇳🇬 Nigeria' },
  { value: 'GH', text: '🇬🇭 Ghana' },
  { value: 'BF', text: '🇧🇫 Burkina Faso' },
  { value: 'ML', text: '🇲🇱 Mali' },
  { value: 'NE', text: '🇳🇪 Niger' },
  { value: 'TD', text: '🇹🇩 Tchad' },
  { value: 'FR', text: '🇫🇷 France' },
  { value: 'BE', text: '🇧🇪 Belgique' },
  { value: 'CH', text: '🇨🇭 Suisse' },
  { value: 'CA', text: '🇨🇦 Canada' },
  { value: 'OTHER', text: '🌍 Autre pays' }
])

// Form state
const form = useForm({
  name: '',
  email: '',
  country: '',
  phone: '',
  study_level: '',
  consent: false
})

// UI State
const stats = reactive({
  students: '2,000+',
  universities: '150+',
  countries: '15+',
  success_rate: '85%'
})

const benefits = reactive([
  {
    icon: '🎯',
    title: 'Accompagnement Complet',
    description: 'De l\'orientation personnalisée jusqu\'à l\'intégration à l\'étranger, nous vous accompagnons dans chaque étape de votre parcours étudiant.'
  },
  {
    icon: '🚀',
    title: 'Réussite Garantie',
    description: 'Accès privilégié à notre réseau d\'universités partenaires, simplification des démarches et solutions de financement personnalisées.'
  },
  {
    icon: '📚',
    title: 'Simplification des Démarches',
    description: 'Nous vous aidons à naviguer facilement dans les démarches administratives complexes pour votre inscription universitaire.'
  },
  {
    icon: '💰',
    title: 'Solutions de Financement',
    description: 'Accès à des bourses d\'études exclusives et conseils personnalisés pour financer vos études à l\'étranger.'
  },
  {
    icon: '🏛️',
    title: 'Réseau d\'Universités',
    description: 'Partenariats privilégiés avec les meilleures universités à l\'étranger pour maximiser vos chances d\'admission.'
  },
  {
    icon: '🌟',
    title: 'Suivi Personnalisé',
    description: 'Un accompagnement individualisé depuis votre candidature jusqu\'à votre réussite académique à l\'étranger.'
  }
])

const testimonials = reactive([
  {
    name: 'Amina M.',
    country: 'Cameroun',
    university: 'ESSEC Business School',
    quote: 'Grâce à EtapSup, j\'ai pu intégrer l\'école de mes rêves en seulement 3 mois !',
    rating: 5
  },
  {
    name: 'Omar S.',
    country: 'Sénégal',
    university: 'Université Paris-Dauphine',
    quote: 'L\'accompagnement personnalisé m\'a permis de naviguer facilement dans les démarches.',
    rating: 5
  },
  {
    name: 'Fatou D.',
    country: 'Côte d\'Ivoire',
    university: 'Sciences Po Paris',
    quote: 'Une équipe exceptionnelle qui m\'a aidée à réaliser mon rêve d\'étudier à l\'étranger.',
    rating: 5
  }
])

// Animation state
const isVisible = ref(false)

// UI State for auth flow
const showAuthModal = ref(false)
const registrationSuccess = ref(false)
const registrationId = ref(null)

// Page et flash data
const page = usePage()

// Form submission with complete flow
const submitForm = () => {
  if (!form.consent) {
    alert('Veuillez accepter les conditions pour continuer')
    return
  }

  form.post(route('events.register'), {
    onSuccess: (page) => {
      console.log('Form submitted successfully')

      // Solution directe: Afficher le popup immédiatement après succès
      registrationSuccess.value = true
      showAuthModal.value = true

      // Reset du formulaire
      form.reset()
    },
    onError: (errors) => {
      console.error('Erreurs:', errors)
    }
  })
}

// Handle modal close - simplified for thank you popup

// Highlight hero form (formulaire maintenant dans hero)
const highlightForm = () => {
  const formElement = document.querySelector('.hero-form-card')
  if (formElement) {
    formElement.classList.add('form-highlight')
    setTimeout(() => {
      formElement.classList.remove('form-highlight')
    }, 2000)
  }
}

// Animation on mount
onMounted(() => {
  setTimeout(() => {
    isVisible.value = true
  }, 300)
})

// Solution simplifiée: Le popup est géré directement dans onSuccess
</script>

<template>
  <Head>
    <title>Étudiez à l'étranger - Webinaire Gratuit | EtapSup</title>
    <meta name="description" content="Découvrez comment intégrer les meilleures universités à l'étranger. Webinaire gratuit avec nos experts - Inscription limitée." />
    <meta name="keywords" content="études à l'étranger, université internationale, étudiant africain, orientation, webinaire" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://etapsup.com/events" />
    <meta property="og:title" content="Étudiez à l'étranger - Webinaire Gratuit | EtapSup" />
    <meta property="og:description" content="Découvrez comment intégrer les meilleures universités à l'étranger. Webinaire gratuit avec nos experts." />
    <meta property="og:image" content="/images/front/event-banner.jpg" />

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="https://etapsup.com/events" />
    <meta property="twitter:title" content="Étudiez à l'étranger - Webinaire Gratuit | EtapSup" />
    <meta property="twitter:description" content="Découvrez comment intégrer les meilleures universités à l'étranger. Webinaire gratuit avec nos experts." />
    <meta property="twitter:image" content="/images/front/event-banner.jpg" />

    <!-- Note: Google Analytics will be implemented at application level in future sprint -->
    <!-- Vue 3 doesn't allow <script> tags with side effects in component templates -->
  </Head>

  <div class="event-landing-page">
    <!-- Hero Section -->
    <section class="hero-section" :class="{ 'animate-in': isVisible }">
      <div class="hero-overlay"></div>
      <BContainer class="hero-content">
        <BRow class="align-items-center min-vh-100">
          <BCol lg="6" class="text-white position-relative z-index-2">
            <div class="hero-badge">
              <span class="badge-text">🔥 Webinaire Exclusif</span>
            </div>

            <h1 class="hero-title">
              Réalisez votre rêve d'étudier à <span class="text-gradient">l'étranger</span>
            </h1>

            <p class="hero-subtitle">
              Découvrez les secrets pour intégrer les meilleures universités à l'étranger.
              Webinaire gratuit avec nos experts - Places limitées !
            </p>

            <div class="hero-stats">
              <div class="stat-item">
                <div class="stat-number">{{ stats.students }}</div>
                <div class="stat-label">Étudiants accompagnés</div>
              </div>
              <div class="stat-divider"></div>
              <div class="stat-item">
                <div class="stat-number">{{ stats.success_rate }}</div>
                <div class="stat-label">Taux de réussite</div>
              </div>
            </div>

            <div class="hero-cta">
              <BButton
                @click="highlightForm"
                variant="primary"
                size="lg"
                class="cta-button cta-primary"
              >
                <span>🎯 Réserver ma place gratuite</span>
              </BButton>

              <p class="cta-subtext">
                <i class="bi bi-clock"></i>
                Plus que {{ 47 }} places disponibles
              </p>
            </div>
          </BCol>

          <BCol lg="6">
            <div class="hero-form-card">
              <div class="form-header">
                <h3 class="form-title">
                  <span class="form-icon">🚀</span>
                  Réservez votre place maintenant
                </h3>
                <p class="form-subtitle">
                  Webinaire gratuit - Jeudi 11 Octobre à 19h (GMT+1)
                </p>
              </div>

              <form @submit.prevent="submitForm" class="hero-registration-form">
                <BFormGroup class="form-group">
                  <BFormInput
                    v-model="form.name"
                    :state="form.errors.name ? false : null"
                    :disabled="form.processing"
                    placeholder="Nom complet *"
                    class="form-input"
                    required
                  />
                  <BFormInvalidFeedback v-if="form.errors.name">
                    {{ form.errors.name }}
                  </BFormInvalidFeedback>
                </BFormGroup>

                <BFormGroup class="form-group">
                  <BFormInput
                    v-model="form.email"
                    type="email"
                    :state="form.errors.email ? false : null"
                    :disabled="form.processing"
                    placeholder="Email *"
                    class="form-input"
                    required
                  />
                  <BFormInvalidFeedback v-if="form.errors.email">
                    {{ form.errors.email }}
                  </BFormInvalidFeedback>
                </BFormGroup>

                <BFormGroup class="form-group">
                  <BFormSelect
                    v-model="form.country"
                    :options="countries"
                    :state="form.errors.country ? false : null"
                    :disabled="form.processing"
                    class="form-input"
                    required
                  />
                  <BFormInvalidFeedback v-if="form.errors.country">
                    {{ form.errors.country }}
                  </BFormInvalidFeedback>
                </BFormGroup>

                <BFormGroup class="form-group">
                  <BFormInput
                    v-model="form.phone"
                    type="tel"
                    :state="form.errors.phone ? false : null"
                    :disabled="form.processing"
                    placeholder="Téléphone (optionnel)"
                    class="form-input"
                  />
                  <BFormInvalidFeedback v-if="form.errors.phone">
                    {{ form.errors.phone }}
                  </BFormInvalidFeedback>
                </BFormGroup>

                <BFormGroup class="form-group">
                  <BFormSelect
                    v-model="form.study_level"
                    :options="[
                      { value: '', text: 'Niveau d\'études (optionnel)' },
                      { value: 'bac', text: 'Baccalauréat' },
                      { value: 'bac+1', text: 'Bac+1' },
                      { value: 'bac+2', text: 'Bac+2' },
                      { value: 'bac+3', text: 'Bac+3 (Licence)' },
                      { value: 'bac+4', text: 'Bac+4' },
                      { value: 'bac+5', text: 'Bac+5 (Master)' }
                    ]"
                    :disabled="form.processing"
                    class="form-input"
                  />
                </BFormGroup>

                <div class="consent-section">
                  <label class="consent-checkbox">
                    <input
                      v-model="form.consent"
                      type="checkbox"
                      :disabled="form.processing"
                      required
                    />
                    <span class="checkmark"></span>
                    <span class="consent-text">
                      J'accepte de recevoir des informations sur les études à l'étranger
                    </span>
                  </label>
                </div>

                <BButton
                  type="submit"
                  :loading="form.processing"
                  :disabled="form.processing || !form.consent"
                  variant="primary"
                  size="lg"
                  class="submit-button w-100"
                >
                  <span v-if="form.processing">Validation en cours...</span>
                  <span v-else>🎓 Réserver ma place gratuite</span>
                </BButton>
              </form>
            </div>
          </BCol>
        </BRow>
      </BContainer>
    </section>

    <!-- Benefits Section -->
    <section class="benefits-section">
      <BContainer>
        <div class="section-header text-center">
          <h2 class="section-title">Ce que vous allez découvrir</h2>
          <p class="section-subtitle">
            Un programme complet pour maximiser vos chances de réussite
          </p>
        </div>

        <BRow class="benefits-grid">
          <BCol
            v-for="(benefit, index) in benefits"
            :key="index"
            md="6"
            lg="4"
            class="mb-4"
          >
            <div class="benefit-card">
              <div class="benefit-icon">{{ benefit.icon }}</div>
              <h3 class="benefit-title">{{ benefit.title }}</h3>
              <p class="benefit-description">{{ benefit.description }}</p>
            </div>
          </BCol>
        </BRow>
      </BContainer>
    </section>


    <!-- Testimonials Section -->
    <section class="testimonials-section">
      <BContainer>
        <div class="section-header text-center">
          <h2 class="section-title">Ils nous ont fait confiance</h2>
          <p class="section-subtitle">
            Découvrez les témoignages de nos étudiants qui étudient maintenant à l'étranger
          </p>
        </div>

        <BRow>
          <BCol
            v-for="(testimonial, index) in testimonials"
            :key="index"
            md="4"
            class="mb-4"
          >
            <div class="testimonial-card">
              <div class="testimonial-rating">
                <span v-for="i in testimonial.rating" :key="i" class="star">⭐</span>
              </div>
              <p class="testimonial-quote">"{{ testimonial.quote }}"</p>
              <div class="testimonial-author">
                <div class="author-avatar">
                  {{ testimonial.name.charAt(0) }}
                </div>
                <div class="author-info">
                  <div class="author-name">{{ testimonial.name }}</div>
                  <div class="author-details">{{ testimonial.country }} → {{ testimonial.university }}</div>
                </div>
              </div>
            </div>
          </BCol>
        </BRow>
      </BContainer>
    </section>

    <!-- Trust Section -->
    <section class="trust-section">
      <BContainer>
        <BRow class="text-center">
          <BCol lg="3" md="6" class="mb-4">
            <div class="trust-stat">
              <div class="trust-number">{{ stats.students }}</div>
              <div class="trust-label">Étudiants accompagnés</div>
            </div>
          </BCol>
          <BCol lg="3" md="6" class="mb-4">
            <div class="trust-stat">
              <div class="trust-number">{{ stats.universities }}</div>
              <div class="trust-label">Universités partenaires</div>
            </div>
          </BCol>
          <BCol lg="3" md="6" class="mb-4">
            <div class="trust-stat">
              <div class="trust-number">{{ stats.countries }}</div>
              <div class="trust-label">Pays représentés</div>
            </div>
          </BCol>
          <BCol lg="3" md="6" class="mb-4">
            <div class="trust-stat">
              <div class="trust-number">{{ stats.success_rate }}</div>
              <div class="trust-label">Taux de réussite</div>
            </div>
          </BCol>
        </BRow>
      </BContainer>
    </section>

    <!-- Footer CTA -->
    <section class="footer-cta">
      <BContainer>
        <div class="cta-content text-center">
          <h2 class="cta-title">Ne ratez pas cette opportunité</h2>
          <p class="cta-text">
            Rejoignez les milliers d'étudiants africains qui réalisent leur rêve d'étudier à l'étranger
          </p>
          <BButton
            @click="highlightForm"
            variant="primary"
            size="lg"
            class="cta-button cta-primary"
          >
Réserver ma place maintenant
          </BButton>
        </div>
      </BContainer>
    </section>

    <!-- Footer EtapSup -->
    <footer class="main-footer">
      <BContainer>
        <div class="footer-content text-center">
          <div class="footer-logo">
            <h4>EtapSup</h4>
          </div>
          <p class="footer-description">
            Votre partenaire de confiance pour réussir vos études à l'étranger.
            Nous accompagnons les étudiants africains dans leur parcours d'excellence académique.
          </p>
          <div class="footer-links">
            <a href="/privacy">Politique de confidentialité</a>
            <span>•</span>
            <a href="/terms">Conditions d'utilisation</a>
            <span>•</span>
            <a href="/contact">Contact</a>
          </div>
          <div class="footer-copyright">
            <p>&copy; 2025 EtapSup. Tous droits réservés. Réalisez votre rêve d'étudier à l'étranger.</p>
          </div>
        </div>
      </BContainer>
    </footer>

    <!-- Thank You Modal -->
    <div v-if="showAuthModal" class="auth-modal-overlay" @click="showAuthModal = false">
      <div class="auth-modal" @click.stop>
        <div class="auth-modal-content">
          <div class="success-icon">🎉</div>
          <h3 class="auth-modal-title">Merci pour votre inscription !</h3>
          <p class="auth-modal-subtitle">
            Votre place est réservée pour le webinaire du 5 octobre à 19h (GMT+1).
            <br><br>
            <strong>Un email de confirmation vous a été envoyé</strong> avec tous les détails pour rejoindre le webinaire.
          </p>

          <div class="auth-modal-actions">
            <BButton
              @click="showAuthModal = false"
              variant="primary"
              size="lg"
              class="auth-cta-button"
            >
              Fermer
            </BButton>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Banner (appears briefly after form submission) -->
    <div v-if="registrationSuccess && !showAuthModal" class="success-banner">
      <div class="success-banner-content">
        <span class="success-icon">✅</span>
        <span class="success-text">Place réservée avec succès !</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Refonte: Story 1.1.1 - Event Landing Page Styles inspired by Diplomeo */

/* Global Styles */
.event-landing-page {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  overflow-x: hidden;
}

/* Hero Section */
.hero-section {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.3);
  z-index: 1;
}

.hero-content {
  position: relative;
  z-index: 2;
}

.hero-section.animate-in .hero-title {
  animation: slideInUp 0.8s ease-out;
}

.hero-section.animate-in .hero-subtitle {
  animation: slideInUp 0.8s ease-out 0.2s both;
}

.hero-section.animate-in .hero-cta {
  animation: slideInUp 0.8s ease-out 0.4s both;
}

.hero-badge {
  display: inline-block;
  margin-bottom: 1.5rem;
}

.badge-text {
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 25px;
  font-size: 0.875rem;
  font-weight: 600;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.hero-title {
  font-size: clamp(2.5rem, 5vw, 4rem);
  font-weight: 800;
  line-height: 1.1;
  margin-bottom: 1.5rem;
  color: white;
}

.text-gradient {
  background: linear-gradient(45deg, #ffd700, #ffed4e);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-subtitle {
  font-size: 1.25rem;
  line-height: 1.6;
  margin-bottom: 2rem;
  opacity: 0.95;
  max-width: 500px;
}

.hero-stats {
  display: flex;
  align-items: center;
  gap: 2rem;
  margin-bottom: 2.5rem;
}

.stat-item {
  text-align: center;
}

.stat-number {
  font-size: 1.75rem;
  font-weight: 700;
  color: #ffd700;
}

.stat-label {
  font-size: 0.875rem;
  opacity: 0.9;
}

.stat-divider {
  width: 1px;
  height: 40px;
  background: rgba(255, 255, 255, 0.3);
}

.hero-cta {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 1rem;
}

.cta-button {
  padding: 1rem 2rem;
  font-size: 1.1rem;
  font-weight: 600;
  border-radius: 50px;
  border: none;
  transition: all 0.3s ease;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.cta-primary {
  background: linear-gradient(45deg, #ed2939, #cc1f2d);
  color: white;
}

.cta-button:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
}

.cta-subtext {
  font-size: 0.875rem;
  opacity: 0.9;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.hero-illustration {
  opacity: 0.9;
  animation: float 6s ease-in-out infinite;
}

/* Benefits Section */
.benefits-section {
  padding: 5rem 0;
  background: #f8fafc;
}

.section-header {
  margin-bottom: 3rem;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 1rem;
}

.section-subtitle {
  font-size: 1.1rem;
  color: #64748b;
  max-width: 600px;
  margin: 0 auto;
}

.benefits-grid {
  gap: 2rem;
  justify-content: center;
}

/* Garantir 3 colonnes bien espacées sur desktop */
@media (min-width: 992px) {
  .benefits-grid .col-lg-4 {
    flex: 0 0 calc(33.333% - 1.333rem);
    max-width: calc(33.333% - 1.333rem);
  }
}

.benefit-card {
  background: white;
  padding: 2rem;
  border-radius: 1rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  text-align: center;
  transition: transform 0.3s ease;
  height: 100%;
}

.benefit-card:hover {
  transform: translateY(-5px);
}

.benefit-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.benefit-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1a202c;
  margin-bottom: 1rem;
}

.benefit-description {
  color: #64748b;
  line-height: 1.6;
  margin: 0;
}

/* Form Section */
.form-section {
  padding: 5rem 0;
  background: white;
}

.form-card {
  background: white;
  border-radius: 1.5rem;
  padding: 3rem;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
}

.form-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.form-title {
  font-size: 2rem;
  font-weight: 700;
  color: white;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.form-icon {
  font-size: 1.5rem;
}

.form-subtitle {
  color: white;
  font-size: 1rem;
  margin: 0;
}

.registration-form {
  margin-top: 2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
}

.form-input {
  padding: 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 1rem;
  transition: all 0.2s ease;
  width: 100%;
}

.form-input:focus {
  outline: none;
  border-color: #ed2939;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.consent-section {
  margin: 2rem 0;
}

.consent-checkbox {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  cursor: pointer;
  font-size: 0.875rem;
  line-height: 1.5;
}

.consent-checkbox input[type="checkbox"] {
  display: none;
}

.checkmark {
  width: 20px;
  height: 20px;
  border: 2px solid #d1d5db;
  border-radius: 4px;
  position: relative;
  flex-shrink: 0;
  transition: all 0.2s ease;
}

.consent-checkbox input[type="checkbox"]:checked + .checkmark {
  background: #ed2939;
  border-color: #ed2939;
}

.consent-checkbox input[type="checkbox"]:checked + .checkmark::after {
  content: '✓';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 12px;
  font-weight: bold;
}

.consent-text a {
  color: #ed2939;
  text-decoration: none;
}

.consent-text a:hover {
  text-decoration: underline;
}

.submit-button {
  background: linear-gradient(45deg, #ed2939, #cc1f2d);
  border: none;
  padding: 1rem 2rem;
  font-size: 1.1rem;
  font-weight: 600;
  border-radius: 0.5rem;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.submit-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.submit-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.form-footer-text {
  text-align: center;
  margin-top: 1rem;
  font-size: 0.875rem;
  color: #64748b;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

/* Testimonials Section */
.testimonials-section {
  padding: 5rem 0;
  background: #f8fafc;
}

.testimonial-card {
  background: white;
  padding: 2rem;
  border-radius: 1rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  height: 100%;
  transition: transform 0.3s ease;
}

.testimonial-card:hover {
  transform: translateY(-5px);
}

.testimonial-rating {
  margin-bottom: 1rem;
}

.star {
  font-size: 1rem;
}

.testimonial-quote {
  font-style: italic;
  line-height: 1.6;
  margin-bottom: 1.5rem;
  color: #374151;
}

.testimonial-author {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.author-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: linear-gradient(45deg, #ed2939, #cc1f2d);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 1.25rem;
}

.author-name {
  font-weight: 600;
  color: #1a202c;
}

.author-details {
  font-size: 0.875rem;
  color: #64748b;
}

/* Trust Section */
.trust-section {
  padding: 3rem 0;
  background: white;
}

.trust-stat {
  text-align: center;
}

.trust-number {
  font-size: 2.5rem;
  font-weight: 700;
  color: #ed2939;
  display: block;
}

.trust-label {
  color: #64748b;
  font-size: 0.875rem;
  margin-top: 0.5rem;
}

/* Footer CTA */
.footer-cta {
  padding: 4rem 0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.cta-content {
  max-width: 600px;
  margin: 0 auto;
}

.cta-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.cta-text {
  font-size: 1.1rem;
  margin-bottom: 2rem;
  opacity: 0.95;
}

/* Animations */
@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}

/* Mobile Optimizations */
@media (max-width: 768px) {
  .hero-title {
    font-size: 2.5rem;
    text-align: center;
  }

  .hero-subtitle {
    text-align: center;
    margin: 0 auto 2rem;
  }

  .hero-stats {
    justify-content: center;
    flex-wrap: wrap;
  }

  .hero-cta {
    align-items: center;
    text-align: center;
  }

  .section-title {
    font-size: 2rem;
  }

  .form-card {
    padding: 2rem 1.5rem;
  }

  .benefits-grid {
    gap: 1rem;
  }

  .cta-title {
    font-size: 2rem;
  }
}

@media (max-width: 576px) {
  .hero-title {
    font-size: 2rem;
  }

  .form-card {
    padding: 1.5rem 1rem;
  }

  .hero-stats {
    gap: 1rem;
  }

  .stat-divider {
    display: none;
  }
}

/* Auth Modal Styles */
.auth-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(5px);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  animation: fadeIn 0.3s ease-out;
}

.auth-modal {
  background: white;
  border-radius: 1.5rem;
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
  animation: slideInUp 0.3s ease-out;
}

.auth-modal-content {
  padding: 3rem 2.5rem;
  text-align: center;
}

.success-icon {
  font-size: 4rem;
  margin-bottom: 1.5rem;
  display: block;
}

.auth-modal-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 1rem;
}

.auth-modal-subtitle {
  color: #64748b;
  font-size: 1rem;
  line-height: 1.6;
  margin-bottom: 2rem;
}

.auth-prompt {
  background: #f8fafc;
  border-radius: 1rem;
  padding: 2rem;
  margin-bottom: 2rem;
  text-align: left;
}

.auth-prompt h4 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1a202c;
  margin-bottom: 0.5rem;
  text-align: center;
}

.auth-prompt p {
  color: #64748b;
  margin-bottom: 1rem;
  text-align: center;
}

.auth-benefits {
  list-style: none;
  padding: 0;
  margin: 0;
}

.auth-benefits li {
  padding: 0.5rem 0;
  color: #374151;
  font-size: 0.875rem;
  line-height: 1.5;
}

.auth-modal-actions {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  align-items: center;
}

.auth-cta-button {
  background: linear-gradient(45deg, #ed2939, #cc1f2d);
  border: none;
  padding: 1rem 2rem;
  font-size: 1.1rem;
  font-weight: 600;
  border-radius: 0.75rem;
  color: white;
  text-decoration: none;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 250px;
}

.auth-cta-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
  color: white;
}

.auth-skip-button {
  background: none;
  border: none;
  color: #64748b;
  font-size: 0.875rem;
  cursor: pointer;
  padding: 0.5rem 1rem;
  transition: color 0.2s ease;
}

.auth-skip-button:hover {
  color: #374151;
}

/* Success Banner */
.success-banner {
  position: fixed;
  top: 2rem;
  right: 2rem;
  background: linear-gradient(45deg, #10b981, #059669);
  color: white;
  padding: 1rem 1.5rem;
  border-radius: 0.75rem;
  box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
  z-index: 1001;
  animation: slideInRight 0.5s ease-out;
}

.success-banner-content {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 500;
}

.success-banner .success-icon {
  font-size: 1.25rem;
}

.success-banner .success-text {
  font-size: 0.875rem;
}

/* Modal Animations */
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(100%);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

/* Mobile Modal Adjustments */
@media (max-width: 768px) {
  .auth-modal-content {
    padding: 2rem 1.5rem;
  }

  .auth-modal-title {
    font-size: 1.5rem;
  }

  .auth-prompt {
    padding: 1.5rem;
  }

  .auth-cta-button {
    min-width: 200px;
    font-size: 1rem;
  }

  .success-banner {
    top: 1rem;
    right: 1rem;
    left: 1rem;
  }
}

/* Main Footer EtapSup */
.main-footer {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 3rem 0 1.5rem;
  margin-top: 0;
}

.footer-content {
  max-width: 600px;
  margin: 0 auto;
}

.footer-logo h4 {
  font-size: 2rem;
  font-weight: 700;
  color: white;
  margin-bottom: 1rem;
}

.footer-description {
  font-size: 1rem;
  line-height: 1.6;
  margin-bottom: 2rem;
  opacity: 0.9;
}

.footer-links {
  margin-bottom: 2rem;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.footer-links a {
  color: white;
  text-decoration: none;
  font-size: 0.875rem;
  transition: color 0.2s ease;
}

.footer-links a:hover {
  color: #ed2939;
  text-decoration: underline;
}

.footer-links span {
  opacity: 0.5;
  font-size: 0.875rem;
}

.footer-copyright p {
  font-size: 0.875rem;
  opacity: 0.7;
  margin: 0;
}

@media (max-width: 768px) {
  .footer-links {
    flex-direction: column;
    gap: 0.5rem;
  }

  .footer-links span {
    display: none;
  }
}

/* Hero Form Highlight Effect */
.hero-form-card.form-highlight {
  transform: scale(1.02);
  box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
  transition: all 0.3s ease;
}

.hero-form-card {
  transition: all 0.3s ease;
}
</style>
