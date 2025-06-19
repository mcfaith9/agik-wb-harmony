<script setup lang="ts">
  import { ref, onMounted, watch } from "vue"
  import axios from "axios"
  import AdminLayout from "@/components/layout/AdminLayout.vue"
  import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue"

  const currentPageTitle = ref("Pokémon Browser")
  const pageDescription = ref("Browse Pokémon using the official PokéAPI")

  const pokemonList = ref<{ name: string; url: string }[]>([])
  const loading = ref(false)
  const page = ref(1)
  const limit = 20

  const fetchPokemon = async () => {
    loading.value = true
    const offset = (page.value - 1) * limit
    try {
      const res = await axios.get(`https://pokeapi.co/api/v2/pokemon?limit=${limit}&offset=${offset}`, {
        withCredentials: false
      })
      pokemonList.value = res.data.results
    } catch (error) {
      console.error("Failed to fetch Pokémon", error)
    } finally {
      loading.value = false
    }
  }

  onMounted(fetchPokemon)
  watch(page, fetchPokemon)

  const nextPage = () => page.value++
  const prevPage = () => {
    if (page.value > 1) page.value--
  }

  const getPokemonImage = (url: string) => {
    const id = url.split("/").filter(Boolean).pop()
    return `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${id}.png`
  }
</script>

<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" :pageDescription="pageDescription" />
    <div class="min-h-screen rounded-2xl border bg-white px-5 py-7 xl:px-10 xl:py-12">
      <div class="mx-auto w-full max-w-[630px] text-center">
        <h3 class="mb-4 font-semibold text-gray-800 text-2xl">Pokémon List</h3>

        <div class="p-4 max-w-3xl mx-auto">
          <div v-if="loading" class="text-center">Loading...</div>
          <div v-else>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div
                v-for="pokemon in pokemonList"
                :key="pokemon.name"
                class="bg-white shadow rounded p-2 text-center">
                <img :src="getPokemonImage(pokemon.url)" class="w-20 h-20 mx-auto mb-2" />
                <p class="capitalize">{{ pokemon.name }}</p>
              </div>
            </div>

            <div class="mt-6 flex justify-between">
              <button @click="prevPage" :disabled="page === 1" class="btn">Prev</button>
              <button @click="nextPage" class="btn">Next</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>