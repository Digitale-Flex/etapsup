<script setup lang="ts">
import AppHead from '@/Components/AppHead.vue';

interface Props {
    message: string;
    description: string;
}

defineProps<Props>();
</script>

<template>
    <AppHead title="EtapSup - Test Page" />

    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <h1 class="text-3xl font-bold text-blue-600">EtapSup</h1>
                        <span class="ml-2 text-sm text-gray-500">Test Page</span>
                    </div>
                    <nav class="hidden md:flex space-x-8">
                        <a href="#" class="text-gray-500 hover:text-gray-900">Établissements</a>
                        <a href="#" class="text-gray-500 hover:text-gray-900">Comment ça marche</a>
                        <a href="#" class="text-gray-500 hover:text-gray-900">Connexion</a>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
                <div class="text-center">
                    <h2 class="text-4xl md:text-6xl font-bold mb-6">
                        {{ message }}
                    </h2>
                    <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                        {{ description }}
                    </p>
                    <button class="bg-white text-blue-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-colors">
                        Découvrir les établissements
                    </button>
                </div>
            </div>
        </section>

        <!-- Search Bar Section -->
        <section class="bg-white py-8 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pays</label>
                            <select class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option>Sélectionner un pays</option>
                                <option>Sénégal</option>
                                <option>Côte d'Ivoire</option>
                                <option>Mali</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ville</label>
                            <input type="text" placeholder="Entrez une ville"
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Domaine d'études</label>
                            <select class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option>Tous les domaines</option>
                                <option>Informatique</option>
                                <option>Business</option>
                                <option>Médecine</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition-colors">
                                Rechercher
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Establishments Grid -->
        <section class="py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h3 class="text-3xl font-bold text-gray-900 mb-8 text-center">
                    Établissements Populaires
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Card 1 -->
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                        <div class="h-48 bg-gradient-to-r from-blue-400 to-purple-500"></div>
                        <div class="p-6">
                            <div class="flex items-center mb-3">
                                <div class="w-12 h-12 bg-gray-200 rounded-full mr-3"></div>
                                <div>
                                    <h4 class="font-semibold text-lg">Université Lorem Ipsum</h4>
                                    <p class="text-gray-600 text-sm">Dakar, Sénégal</p>
                                </div>
                            </div>
                            <p class="text-gray-600 mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt.</p>
                            <p class="text-sm text-blue-600 mb-4">12 programmes disponibles</p>
                            <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition-colors">
                                Voir les programmes
                            </button>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                        <div class="h-48 bg-gradient-to-r from-green-400 to-blue-500"></div>
                        <div class="p-6">
                            <div class="flex items-center mb-3">
                                <div class="w-12 h-12 bg-gray-200 rounded-full mr-3"></div>
                                <div>
                                    <h4 class="font-semibold text-lg">École Superieure Dolor</h4>
                                    <p class="text-gray-600 text-sm">Abidjan, Côte d'Ivoire</p>
                                </div>
                            </div>
                            <p class="text-gray-600 mb-4">Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.</p>
                            <p class="text-sm text-blue-600 mb-4">8 programmes disponibles</p>
                            <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition-colors">
                                Voir les programmes
                            </button>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                        <div class="h-48 bg-gradient-to-r from-purple-400 to-pink-500"></div>
                        <div class="p-6">
                            <div class="flex items-center mb-3">
                                <div class="w-12 h-12 bg-gray-200 rounded-full mr-3"></div>
                                <div>
                                    <h4 class="font-semibold text-lg">Institut Adipiscing</h4>
                                    <p class="text-gray-600 text-sm">Bamako, Mali</p>
                                </div>
                            </div>
                            <p class="text-gray-600 mb-4">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore.</p>
                            <p class="text-sm text-blue-600 mb-4">15 programmes disponibles</p>
                            <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition-colors">
                                Voir les programmes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section class="bg-gray-100 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h3 class="text-3xl font-bold text-gray-900 mb-12 text-center">
                    Comment ça marche
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-white text-2xl font-bold">1</span>
                        </div>
                        <h4 class="text-xl font-semibold mb-2">Découvrez</h4>
                        <p class="text-gray-600">Explorez les établissements et programmes qui correspondent à vos aspirations</p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-white text-2xl font-bold">2</span>
                        </div>
                        <h4 class="text-xl font-semibold mb-2">Postulez</h4>
                        <p class="text-gray-600">Soumettez votre candidature en ligne de manière simple et sécurisée</p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-white text-2xl font-bold">3</span>
                        </div>
                        <h4 class="text-xl font-semibold mb-2">Suivez</h4>
                        <p class="text-gray-600">Trackez vos demandes et recevez des mises à jour en temps réel</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h4 class="text-2xl font-bold mb-4">EtapSup</h4>
                    <p class="text-gray-400">Votre passerelle vers l'enseignement supérieur en Afrique</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Styles spécifiques si nécessaire */
</style>
