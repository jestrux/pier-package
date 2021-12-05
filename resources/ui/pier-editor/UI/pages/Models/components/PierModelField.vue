<template>
    <c-box rounded="md" :color="color" px="4" py="3" background="#222">
        <c-box mb="1" d="flex" align-items="center" justify-content="space-between">
            <c-box d="flex" align-items="center">
                <c-box mr="3">
                    <MDIcon :icon="icon" :size="20" />
                </c-box>

                <c-text fontSize="xl" :color="color">
                    {{ label }}
                </c-text>
            </c-box>
            <c-text v-if="type && type.length" fontSize="lg" color="#777">
                {{ type }}
            </c-text>
        </c-box>
    </c-box>
</template>

<script>

import { CBox, CText } from '@chakra-ui/vue';

import MDIcon from "./MDIcon";

export default {
  name: "PierModelField",
    props: {
        icon: String,
        type: String,
        label: String,
        color: {
            type: String,
            default: "#999"
        }
    },
    components: {
        CBox, 
        CText,
        MDIcon
    }
}
</script>