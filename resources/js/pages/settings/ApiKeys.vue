<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { AlertCircle, Check, Key, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Settings',
        href: '/settings',
    },
    {
        title: 'API Keys',
        href: '/settings/api-keys',
    },
];

const providers = [
    { id: 'openai', name: 'OpenAI' },
    { id: 'anthropic', name: 'Anthropic' },
    { id: 'gemini', name: 'Gemini' },
    { id: 'openrouter', name: 'OpenRouter' },
    { id: 'deepseek', name: 'DeepSeek' },
];

const providerStatus = ref(page.props.providers || {});

const forms: Record<string, ReturnType<typeof useForm>> = {} as any;
providers.forEach((p) => {
    forms[p.id] = useForm({ api_key: '' });
});

const showApiKey = ref<string | null>(null);
const isValidating = ref<Record<string, boolean>>({});

const updateApiKey = (provider: string) => {
    const form = forms[provider];
    form.put('/settings/api-keys', {
        data: { provider, api_key: form.api_key },
        onStart: () => {
            isValidating.value[provider] = true;
        },
        onSuccess: () => {
            form.reset();
            providerStatus.value[provider].hasKey = true;
            alert(`Success: Your ${provider} API key has been updated successfully.`);
        },
        onFinish: () => {
            isValidating.value[provider] = false;
        },
    });
};

const deleteApiKey = (provider: string) => {
    if (confirm('Are you sure you want to delete your API key? You will need to use the system API key or add a new one.')) {
        const form = forms[provider];
        form.delete('/settings/api-keys', {
            data: { provider },
            onSuccess: () => {
                providerStatus.value[provider].hasKey = false;
                alert(`Your ${provider} API key has been removed.`);
            },
        });
    }
};
</script>

<template>
    <Head title="API Keys" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <SettingsLayout>
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium">API Keys</h3>
                    <p class="text-sm text-muted-foreground">Manage your API keys for AI services</p>
                </div>

                <Card v-for="provider in providers" :key="provider.id">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Key class="h-5 w-5" />
                            {{ provider.name }} API Key
                        </CardTitle>
                        <CardDescription>
                            <span v-if="!providerStatus[provider.id]?.hasKey">No API key configured.</span>
                            <span v-else-if="providerStatus[provider.id]?.isUsingEnvKey">Using API key from environment (.env file).</span>
                            <span v-else>Using API key configured in settings.</span>
                        </CardDescription>
                    </CardHeader>

                    <form @submit.prevent="updateApiKey(provider.id)">
                        <CardContent class="space-y-4">
                            <div v-if="providerStatus[provider.id]?.hasKey" class="rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
                                <div class="flex items-center gap-2">
                                    <Check class="h-5 w-5 text-green-600 dark:text-green-400" />
                                    <p class="text-sm font-medium text-green-800 dark:text-green-200">API Key Configured</p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label :for="`new-${provider.id}-api-key`">
                                    {{ providerStatus[provider.id]?.hasKey ? 'Update API Key' : 'Add API Key' }}
                                </Label>
                                <div class="relative">
                                    <Input
                                        :id="`new-${provider.id}-api-key`"
                                        v-model="forms[provider.id].api_key"
                                        :type="showApiKey === provider.id ? 'text' : 'password'"
                                        placeholder="sk-..."
                                        :disabled="forms[provider.id].processing"
                                        class="pr-20"
                                    />
                                    <button
                                        type="button"
                                        @click="showApiKey = showApiKey === provider.id ? null : provider.id"
                                        class="absolute top-1/2 right-2 -translate-y-1/2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                                    >
                                        {{ showApiKey === provider.id ? 'Hide' : 'Show' }}
                                    </button>
                                </div>
                            </div>

                            <div
                                v-if="forms[provider.id].errors.api_key"
                                class="flex items-center gap-2 rounded-md border border-red-500 bg-red-50 p-3 text-sm text-red-800 dark:border-red-700 dark:bg-red-950 dark:text-red-200"
                            >
                                <AlertCircle class="h-4 w-4" />
                                <span>{{ forms[provider.id].errors.api_key }}</span>
                            </div>
                        </CardContent>

                        <CardFooter class="flex gap-3">
                            <Button type="submit" :disabled="forms[provider.id].processing || !forms[provider.id].api_key">
                                <span v-if="isValidating[provider.id]">Validating...</span>
                                <span v-else>{{ providerStatus[provider.id]?.hasKey ? 'Update' : 'Add' }} API Key</span>
                            </Button>

                            <Button v-if="providerStatus[provider.id]?.hasKey" type="button" variant="destructive" @click="deleteApiKey(provider.id)" :disabled="forms[provider.id].processing">
                                <Trash2 class="mr-2 h-4 w-4" />
                                Delete API Key
                            </Button>
                        </CardFooter>
                    </form>
                </Card>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
