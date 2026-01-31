<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, onMounted, watch } from 'vue';

defineOptions({
    name: 'Translations',
});

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

interface Locale {
    id: number;
    code: string;
    name: string;
}

const tags = ['Header', 'Home', 'Form'];
const locales = ref<Locale[]>([]);
const selectedLocale = ref('en');
const translations = ref<Record<string, string>>({});

const fetchLocales = async () => {
    const res = await axios.get('/api/locales');
    locales.value = res.data;

    if (locales.value.length) {
        selectedLocale.value = locales.value[0].code;
    }
};

const fetchTranslations = async () => {
    if (!selectedLocale.value) return;

    const res = await axios.get(
        `/api/translations/export?locale=${selectedLocale.value}&tags=${tags}`,
    );

    translations.value = res.data;
};

watch(selectedLocale, () => {
    fetchTranslations();
});

onMounted(async () => {
    await fetchLocales();
});
</script>

<style scoped>
@import url('https://rsms.me/inter/inter.css');

div {
    font-family: 'Inter', sans-serif;
}

option {
    color: black;
}
</style>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="p-4">
        <h1 class="mb-4 text-2xl font-bold">{{ translations['app.title'] }}</h1>
        <h2 class="mb-4 text-xl font-bold">
            {{ translations['message.welcome'] }}
        </h2>
        <p>{{ translations['app.description'] }}</p>

        <div class="mb-4">
            <label class="mr-2" for="lang"
                >{{ translations['button.language'] }}:</label
            >
            <select
                id="lang"
                v-model="selectedLocale"
                @change="fetchTranslations"
            >
                <option
                    v-for="locale in locales"
                    :key="locale.id"
                    :value="locale.code"
                >
                    {{ locale.name }}
                </option>
            </select>
            <p>
                <a href="/api/documentation" target="_blank">
                    <button
                        class="mt-4 rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600"
                    >
                        {{ translations['link.docs'] }}
                    </button>
                </a>
            </p>
            <div class="mt-4">
                <h4 class="mb-2 font-bold">
                    {{ translations['form.credentials'] }}
                </h4>
                <div class="mb-2">
                    <p>
                        <b class="mb-1 block" for="email"
                            >{{ translations['form.email'] }}:</b
                        >
                        <span>test@example.com</span>
                    </p>
                    <br />
                    <p>
                        <b class="mb-1 block" for="password"
                            >{{ translations['form.password'] }}:</b
                        >
                        <span>password</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
