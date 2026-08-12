<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Camera, Check, RotateCcw, Trash2, X } from 'lucide-vue-next';
import { nextTick, onBeforeUnmount, ref } from 'vue';

defineProps<{
    existingUrl?: string | null;
}>();

const file = defineModel<File | null>('file', { default: null });
const remove = defineModel<boolean>('remove', { default: false });

const videoRef = ref<HTMLVideoElement | null>(null);
const stream = ref<MediaStream | null>(null);
const cameraOpen = ref(false);
const reviewing = ref(false);
const capturedBlob = ref<Blob | null>(null);
const capturedPreviewUrl = ref<string | null>(null);
const confirmedPreviewUrl = ref<string | null>(null);
const errorMessage = ref<string | null>(null);

function revoke(url: string | null) {
    if (url) {
        URL.revokeObjectURL(url);
    }
}

function stopStream() {
    stream.value?.getTracks().forEach((track) => track.stop());
    stream.value = null;
}

async function openCamera() {
    errorMessage.value = null;

    if (!navigator.mediaDevices?.getUserMedia) {
        errorMessage.value = 'Camera is not supported in this browser.';
        return;
    }

    try {
        stream.value = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
        cameraOpen.value = true;
        reviewing.value = false;
        await nextTick();
        if (videoRef.value) {
            videoRef.value.srcObject = stream.value;
        }
    } catch {
        errorMessage.value = 'Could not access the camera. Check permissions and that a camera is connected.';
    }
}

function cancelCamera() {
    stopStream();
    cameraOpen.value = false;
}

function capture() {
    const video = videoRef.value;
    if (!video) {
        return;
    }

    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    if (!ctx) {
        return;
    }
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    canvas.toBlob((blob) => {
        if (!blob) {
            return;
        }
        revoke(capturedPreviewUrl.value);
        capturedBlob.value = blob;
        capturedPreviewUrl.value = URL.createObjectURL(blob);
        reviewing.value = true;
        stopStream();
    }, 'image/jpeg', 0.9);
}

function retake() {
    revoke(capturedPreviewUrl.value);
    capturedPreviewUrl.value = null;
    capturedBlob.value = null;
    reviewing.value = false;
    openCamera();
}

function cancelReview() {
    revoke(capturedPreviewUrl.value);
    capturedPreviewUrl.value = null;
    capturedBlob.value = null;
    reviewing.value = false;
    cameraOpen.value = false;
}

function confirmPhoto() {
    if (!capturedBlob.value) {
        return;
    }

    const blob = capturedBlob.value;

    revoke(confirmedPreviewUrl.value);
    confirmedPreviewUrl.value = capturedPreviewUrl.value;
    capturedPreviewUrl.value = null;
    capturedBlob.value = null;
    reviewing.value = false;
    cameraOpen.value = false;

    file.value = new File([blob], `patient-photo-${Date.now()}.jpg`, { type: 'image/jpeg' });
    remove.value = false;
}

function removePhoto() {
    revoke(confirmedPreviewUrl.value);
    confirmedPreviewUrl.value = null;
    file.value = null;
    remove.value = true;
}

onBeforeUnmount(() => {
    stopStream();
    revoke(capturedPreviewUrl.value);
    revoke(confirmedPreviewUrl.value);
});
</script>

<template>
    <div class="space-y-2">
        <!-- Reviewing a just-captured frame -->
        <div v-if="reviewing" class="space-y-2">
            <img :src="capturedPreviewUrl!" alt="Captured photo preview" class="h-40 w-40 rounded-md border object-cover" />
            <div class="flex gap-2">
                <Button type="button" size="sm" variant="outline" @click="retake">
                    <RotateCcw class="size-4" /> Retake
                </Button>
                <Button type="button" size="sm" @click="confirmPhoto">
                    <Check class="size-4" /> Use Photo
                </Button>
                <Button type="button" size="sm" variant="ghost" @click="cancelReview">
                    <X class="size-4" /> Cancel
                </Button>
            </div>
        </div>

        <!-- Live camera feed -->
        <div v-else-if="cameraOpen" class="space-y-2">
            <video ref="videoRef" autoplay playsinline muted class="h-40 w-40 rounded-md border object-cover"></video>
            <div class="flex gap-2">
                <Button type="button" size="sm" @click="capture">
                    <Camera class="size-4" /> Capture
                </Button>
                <Button type="button" size="sm" variant="ghost" @click="cancelCamera">
                    <X class="size-4" /> Cancel
                </Button>
            </div>
        </div>

        <!-- Idle: show confirmed/existing photo or a placeholder -->
        <div v-else class="space-y-2">
            <img
                v-if="confirmedPreviewUrl"
                :src="confirmedPreviewUrl"
                alt="Captured patient photo"
                class="h-40 w-40 rounded-md border object-cover"
            />
            <img
                v-else-if="existingUrl && !remove"
                :src="existingUrl"
                alt="Patient photo"
                class="h-40 w-40 rounded-md border object-cover"
            />
            <div v-else class="flex h-40 w-40 items-center justify-center rounded-md border border-dashed bg-muted text-muted-foreground">
                <Camera class="size-8" />
            </div>

            <div class="flex gap-2">
                <Button v-if="confirmedPreviewUrl || (existingUrl && !remove)" type="button" size="sm" variant="outline" @click="openCamera">
                    <RotateCcw class="size-4" /> Retake Photo
                </Button>
                <Button v-else type="button" size="sm" variant="outline" @click="openCamera">
                    <Camera class="size-4" /> Open Camera
                </Button>
                <Button
                    v-if="confirmedPreviewUrl || (existingUrl && !remove)"
                    type="button"
                    size="sm"
                    variant="ghost"
                    class="text-destructive hover:text-destructive"
                    @click="removePhoto"
                >
                    <Trash2 class="size-4" /> Remove Photo
                </Button>
            </div>

            <p v-if="errorMessage" class="text-sm text-destructive">{{ errorMessage }}</p>
        </div>
    </div>
</template>
