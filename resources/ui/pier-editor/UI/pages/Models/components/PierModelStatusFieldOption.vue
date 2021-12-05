<style>
    [data-chakra-component="CPopoverContent"]:focus{
        box-shadow: 0 0 0 2px #545353;
    }
    
    [data-chakra-component="CPopoverContent"] [data-chakra-component="CIconButton"]{
        height: 26px;
        width: 26px;
        min-width: 0;
    }
    
    [data-chakra-component="CPopoverHeader"]{
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
        margin-bottom: 0.2rem;
        border-color: #616160;
    }
</style>
<template>
    <c-box>
        <!-- <PierModelFieldOption v-model="options.valueType" /> -->

        <c-form-control mb="6">
            <c-form-label color="#777" fontSize="lg">
                Statuses
            </c-form-label>
            
            <c-box d="grid" gridRowGap="5" gridColumnGap="5" gridTemplateColumns="repeat(4, 1fr);">
                <template v-for="(option, index) in statusOptions">
                    <c-box :key="index" pos="relative" rounded="full">
                        <c-popover placement="top" :ref="`statusColorPopover${index}`">
                            <c-popover-trigger @click="resetPopover(); activeOption = index;">
                                <c-box rounded="sm" pos="absolute" left="10px" top="0" bottom="0" my="auto" w="18px" h="18px" zIndex="2" cursor="pointer" :bg="option.color"    
                                    d="flex" alignItems="center" justifyContent="center"
                                >
                                    <span style="display: block; padding: 0.2rem; color: white" v-html="icons[option.icon]"></span>
                                </c-box>
                            </c-popover-trigger>
                            <c-popover-content
                                background-color="#414141"
                                border-color="#616160"
                                z-index="4" 
                                width="165px"
                            >
                                <c-popover-arrow />
                                <c-popover-close-button 
                                    @click="resetPopover(index)"
                                />
                                <c-popover-header>
                                    <c-box d="grid" gridGap="1" alignItems="center" gridTemplateColumns="repeat(5, 1fr)">
                                        <c-button
                                            variant="ghost"
                                            style="box-shadow: none !important"
                                            :variant-color="popoverSection == 'color' ? 'orange' : 'white'"
                                            @click="popoverSection = 'color'"
                                        >
                                            Color
                                        </c-button>
                                        <span style="font-size: 2rem; line-height: 0; margin-bottom: 0.3rem; padding-left: 0.3rem;">&middot;</span>
                                        <c-button
                                            variant="ghost"
                                            style="box-shadow: none !important"
                                            :variant-color="popoverSection == 'icon' ? 'orange' : 'white'"
                                            @click="popoverSection = 'icon'"
                                        >
                                            Icon
                                        </c-button>
                                    </c-box>
                                </c-popover-header>
                                <c-popover-body>
                                    <c-box d="grid" gridGap="1" gridTemplateColumns="repeat(5, 1fr)"
                                        ml="-4px">

                                        <template v-if="popoverSection == 'color'">
                                            <c-icon-button
                                                v-for="(color, index2) in colorChoices"
                                                isRound
                                                :key="index2"
                                                :bg="color"
                                                icon="check"
                                                :aria-label="'Select color ' + color"
                                                :color="option.color === color ? 'rgba(0, 0, 0, 0.3)' : 'transparent'"
                                                @click="setStatusColor(index, color)" />
                                        </template>
                                            
                                        <button
                                            v-else
                                            v-for="(icon, index3) in iconChoices"
                                            isRound
                                            :key="index3"
                                            :aria-label="icon.name"
                                            style="box-shadow: none !important; outline: none"
                                            @click="setStatusIcon(index, icon)"
                                            type="button"
                                            p="1"
                                        >
                                            <span 
                                                style="display: block; padding: 0.2rem; position: relative;"
                                                :style="{color: option.icon === icon ? 'rgba(255, 255, 255, 0.6)' : 'rgba(255, 255, 255, 0.2)'}" 
                                                v-html="icons[icon]"
                                            ></span>
                                        </button>

                                            <!-- <c-icon v-if="option.color === color" icon="check" /> -->
                                        <!-- </c-icon-button> -->
                                    </c-box>
                                </c-popover-body>
                            </c-popover-content>
                        </c-popover>
                        

                        <c-input
                            :ref="`statusNameInput${index}`"
                            paddingLeft="36px"
                            minWidth="100px"
                            :id="option.label"
                            type="text"
                            size="md"
                            placeholder="enter status"
                            v-model="option.name"
                        />
                        
                        <c-icon-button
                            @click="removeStatus(index)"
                            position="absolute"
                            right="0" top="0" bottom="0" my="auto"
                            zIndex="2"
                            :opacity="statusOptions.length > 2 ? 0.4 : 0.15"
                            :pointerEvents="statusOptions.length > 2 ? '' : 'none'"
                            variant="ghost"
                            variant-color="white"
                            aria-label="Remove status"
                            icon="close"
                        />
                    </c-box>

                </template>

                <c-button size="md" align-self="flex-start" variant="outline" color="#777" 
                    @click="addStatus">
                    Add Status
                </c-button>
            </c-box>
        </c-form-control>

        <!-- <PierModelFieldOption 
            :key="selectedModel.name + `${selectedField ? selectedField.label : 'field'}`" 
            v-model="field" 
        /> -->
    </c-box>
</template>

<script>

import {
    CIconButton,
    CBox,
    CFormControl, 
    CFormLabel, 
    CInput,
    CButton,
    CPopover,
    CPopoverTrigger,
    CPopoverContent,
    CPopoverHeader,
    CPopoverBody,
    CPopoverArrow,
    CPopoverCloseButton,
} from "@chakra-ui/vue";

import { mapState } from 'vuex';
import PierModelFieldOption from "./PierModelFieldOption";

const icons = {
    "game": '<svg width="100%" viewBox="0 0 512 512"><path d="M467.51 248.83c-18.4-83.18-45.69-136.24-89.43-149.17A91.5 91.5 0 00352 96c-26.89 0-48.11 16-96 16s-69.15-16-96-16a99.09 99.09 0 00-27.2 3.66C89 112.59 61.94 165.7 43.33 248.83c-19 84.91-15.56 152 21.58 164.88 26 9 49.25-9.61 71.27-37 25-31.2 55.79-40.8 119.82-40.8s93.62 9.6 118.66 40.8c22 27.41 46.11 45.79 71.42 37.16 41.02-14.01 40.44-79.13 21.43-165.04z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"/><circle fill="currentColor" cx="292" cy="224" r="20"/><path fill="currentColor" d="M336 288a20 20 0 1120-19.95A20 20 0 01336 288z"/><circle fill="currentColor" cx="336" cy="180" r="20"/><circle fill="currentColor" cx="380" cy="224" r="20"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M160 176v96M208 224h-96"/></svg>',
    "music": '<svg width="100%" viewBox="0 0 512 512"><path d="M83 384c-13-33-35-93.37-35-128C48 141.12 149.33 48 256 48s208 93.12 208 208c0 34.63-23 97-35 128" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path d="M108.39 270.13l-13.69 8h0c-30.23 17.7-31.7 72.41-3.38 122.2s75.87 75.81 106.1 58.12h0l13.69-8a16.16 16.16 0 005.78-21.87L130 276a15.74 15.74 0 00-21.61-5.87zM403.61 270.13l13.69 8h0c30.23 17.69 31.74 72.4 3.38 122.19s-75.87 75.81-106.1 58.12h0l-13.69-8a16.16 16.16 0 01-5.78-21.87L382 276a15.74 15.74 0 0121.61-5.87z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"/></svg>',
    "leaf": '<svg width="100%" viewBox="0 0 512 512"><path d="M321.89 171.42C233 114 141 155.22 56 65.22c-19.8-21-8.3 235.5 98.1 332.7 77.79 71 197.9 63.08 238.4-5.92s18.28-163.17-70.61-220.58zM173 253c86 81 175 129 292 147" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg>',
    "triangle": '<svg width="100%" viewBox="0 0 512 512"><path d="M229.73 45.88L37.53 327.79a31.79 31.79 0 0011.31 46L241 476.26a31.77 31.77 0 0029.92 0l192.2-102.51a31.79 31.79 0 0011.31-46L282.27 45.88a31.8 31.8 0 00-52.54 0zM256 32v448" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg>',
    "school": '<svg width="100%" viewBox="0 0 512 512"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M32 192L256 64l224 128-224 128L32 192z"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M112 240v128l144 80 144-80V240M480 368V192M256 320v128"/></svg>',
    "tshirt": '<svg width="100%" viewBox="0 0 512 512"><path d="M314.56 48s-22.78 8-58.56 8-58.56-8-58.56-8a31.94 31.94 0 00-10.57 1.8L32 104l16.63 88 48.88 5.52a24 24 0 0121.29 24.58L112 464h288l-6.8-241.9a24 24 0 0121.29-24.58l48.88-5.52L480 104 325.13 49.8a31.94 31.94 0 00-10.57-1.8zM333.31 52.66a80 80 0 01-154.62 0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg>',
    "temperature": '<svg width="100%" viewBox="0 0 512 512"><path d="M307.72 302.27a8 8 0 01-3.72-6.75V80a48 48 0 00-48-48h0a48 48 0 00-48 48v215.52a8 8 0 01-3.71 6.74 97.51 97.51 0 00-44.19 86.07A96 96 0 00352 384a97.49 97.49 0 00-44.28-81.73zM256 112v272" fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32"/><circle fill="currentColor" cx="256" cy="384" r="48"/></svg>',
    "time": '<svg width="100%" viewBox="0 0 512 512"><rect x="112" y="112" width="288" height="288" rx="64" ry="64" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><path d="M176 112V40a8 8 0 018-8h144a8 8 0 018 8v72M336 400v72a8 8 0 01-8 8H184a8 8 0 01-8-8v-72" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/></svg>',
    "drop": '<svg width="100%" viewBox="0 0 512 512"><path d="M400 320c0 88.37-55.63 144-144 144s-144-55.63-144-144c0-94.83 103.23-222.85 134.89-259.88a12 12 0 0118.23 0C296.77 97.15 400 225.17 400 320z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"/><path d="M344 328a72 72 0 01-72 72" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg>',
    "speed": '<svg width="100%" viewBox="0 0 512 512"><path fill="currentColor" d="M326.1 231.9l-47.5 75.5a31 31 0 01-7 7 30.11 30.11 0 01-35-49l75.5-47.5a10.23 10.23 0 0111.7 0 10.06 10.06 0 012.3 14z"/><path d="M256 64C132.3 64 32 164.2 32 287.9a223.18 223.18 0 0056.3 148.5c1.1 1.2 2.1 2.4 3.2 3.5a25.19 25.19 0 0037.1-.1 173.13 173.13 0 01254.8 0 25.19 25.19 0 0037.1.1l3.2-3.5A223.18 223.18 0 00480 287.9C480 164.2 379.7 64 256 64z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" d="M256 128v32M416 288h-32M128 288H96M165.49 197.49l-22.63-22.63M346.51 197.49l22.63-22.63"/></svg>',
    "power": '<svg width="100%" viewBox="0 0 512 512"><path d="M463.1 112.37C373.68 96.33 336.71 84.45 256 48c-80.71 36.45-117.68 48.33-207.1 64.37C32.7 369.13 240.58 457.79 256 464c15.42-6.21 223.3-94.87 207.1-351.63z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path fill="currentColor" d="M256 48c-80.71 36.45-117.68 48.33-207.1 64.37C32.7 369.13 240.58 457.79 256 464z"/></svg>',
    "target": '<svg width="100%" viewBox="0 0 512 512"><path d="M448 256c0-106-86-192-192-192S64 150 64 256s86 192 192 192 192-86 192-192z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"/><circle fill="currentColor" cx="256" cy="256" r="144"/></svg>',
    "rain": '<svg width="100%" viewBox="0 0 512 512"><path d="M114.61 162.85A16.07 16.07 0 00128 149.6C140.09 76.17 193.63 32 256 32c57.93 0 96.62 37.75 112.2 77.74a15.84 15.84 0 0012.2 9.87c50 8.15 91.6 41.54 91.6 99.59 0 59.4-48.6 100.8-108 100.8H130c-49.5 0-90-24.7-90-79.2 0-48.47 38.67-72.22 74.61-77.95z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M144 384l-32 48M224 384l-64 96M304 384l-32 48M384 384l-64 96"/></svg>',
    "list": '<svg width="100%" viewBox="0 0 512 512"><rect x="96" y="48" width="320" height="416" rx="48" ry="48" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M176 128h160M176 208h160M176 288h80"/></svg>',
    "mail": '<svg width="100%" viewBox="0 0 512 512"><rect x="48" y="96" width="416" height="320" rx="40" ry="40" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M112 160l144 112 144-112"/></svg>',
    "diamond": '<svg width="100%" viewBox="0 0 512 512"><path d="M35.42 188.21l207.75 269.46a16.17 16.17 0 0025.66 0l207.75-269.46a16.52 16.52 0 00.95-18.75L407.06 55.71A16.22 16.22 0 00393.27 48H118.73a16.22 16.22 0 00-13.79 7.71L34.47 169.46a16.52 16.52 0 00.95 18.75zM48 176h416" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M400 64l-48 112-96-128M112 64l48 112 96-128M256 448l-96-272M256 448l96-272"/></svg>',
    "workout": '<svg width="100%" viewBox="0 0 512 512"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M48 256h416"/><rect x="384" y="128" width="32" height="256" rx="16" ry="16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><rect x="96" y="128" width="32" height="256" rx="16" ry="16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><rect x="32" y="192" width="16" height="128" rx="8" ry="8" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><rect x="464" y="192" width="16" height="128" rx="8" ry="8" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg>',
    "circles": '<svg width="100%" viewBox="0 0 512 512"><circle cx="256" cy="184" r="120" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><circle cx="344" cy="328" r="120" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><circle cx="168" cy="328" r="120" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/></svg>',
};

export default {
    name: "PierModelStatusFieldOption",
    props: {
        value: Object
    },
    mounted() {
        const options = {...this.value};
        console.log("Status field options", options);

        if(options){
            const {availableStatuses} = options;
            if(availableStatuses && availableStatuses.value !== undefined)
                this.statusOptions = availableStatuses.value;
            else{
                this.statusOptions = [
                    { name: "Pending", color: "#ff9800" },
                    { name: "Complete", color: "#4caf50" },
                ];
            }
        }
        else{
            this.statusOptions = [
                { name: "Pending", color: "#ff9800" },
                { name: "Complete", color: "#4caf50" },
            ];
        }
    },
    methods: {
        addStatus(){
            if(this.activeOption != -1)
                this.resetPopover();

            const newStatusIndex = JSON.parse(JSON.stringify(this.statusOptions.length));
            this.statusOptions.push(
                { name: "", color: this.getRandomColor() },
            );

            this.$nextTick(() => {
                const newStatus = this.$refs[`statusNameInput${newStatusIndex}`][0];
                if(newStatus)
                    newStatus.$el.focus();
            });
        },
        removeStatus(index){
            this.statusOptions.splice(index, 1);
        },
        resetPopover(index){
            const popover = this.$refs[`statusColorPopover${index == undefined ? this.activeOption : index}`]
            if(popover)
                popover[0].closePopover();

            if(index != undefined){
                const statusInput = this.$refs[`statusNameInput${index}`][0];
                if(statusInput && !statusInput.$el.value.length)
                    statusInput.$el.focus();
            }

            this.popoverSection = "color";
            this.activeOption = -1;
        },
        setStatusColor(index, color){
            let updatedStatus = JSON.parse(JSON.stringify(this.statusOptions[index]));
            updatedStatus.color = color;
            this.statusOptions.splice(index, 1, updatedStatus);
        },
        setStatusIcon(index, icon){
            let updatedStatus = JSON.parse(JSON.stringify(this.statusOptions[index]));
            updatedStatus.icon = icon;
            this.statusOptions.splice(index, 1, updatedStatus);
        },
        getRandomColor(){
            const usedColors = this.statusOptions.map(({color}) => color);
            let availablColors = this.colorChoices.filter(color => !usedColors.includes(color));
            if(!availablColors.length)
                availablColors = this.colorChoices;

            return availablColors[Math.floor(Math.random() * availablColors.length)];
        }
    },
    data: function(){
        return {
            activeOption: -1,
            icons,
            iconChoices: Object.keys(icons),
            popoverSection: "color",
            valueType:{
                label: "Value Type",
                type: "radio",
                choices: [
                    "Static",
                    "Derived"
                ],
                defaultValue: "Static"
            },
            statusOptions: [],
            colorChoices: [
                "#f44336", //red
                "#E91E63", //pink
                "#9c27b0", //purple
                // "#3f51b5", //violet
                "#2196f3", //blue
                "#009688", //beforegreen
                "#4caf50", //green
                "#ff9800", //orange
                "#607d8b", //bluegrey
            ],
            selectedModel: {},
            selectedField: {},
            options: {}
        }
    },
    computed: {
        ...mapState(['models']),
        field: {
            get: function(){
                const field = {
                    label: "Preview Field",
                    type: "choice",
                    choices: []
                };

                if(this.models && this.models.length && this.selectedModel.fields){
                    const fields = this.selectedModel.fields;
                    field.choices = fields.map(({label}) => label);

                    if(this.selectedField)
                        field.value = this.selectedField.label;
                }

                return field;
            },
            set: function(newField) {
                if(this.selectedModel.fields){
                    const fields = this.selectedModel.fields;
                    this.selectedField = fields.find(({label}) => label === newField.value);
                }
            }
        }
    },
    watch: {
        statusOptions: {
            immediate: true,
            deep: true,
            handler: function(){
                const options = {...this.options};
                let newValue = this.statusOptions;
                newValue = newValue.filter(({name}) => name && name.length);

                console.log("new status options: ", newValue);
                if(!newValue.length){
                    delete options.availableStatuses;
                    this.options = options;
                    return;
                }

                this.options = {
                    ...options, 
                    availableStatuses: { value: newValue }
                };
            }
        },
        options: {
            immediate: true,
            deep: true,
            handler: function(){
                const newValue = this.options;
                console.log("new options value: ", newValue);
                this.$emit('input', newValue);
            }
        }
    },
    components: {
        CIconButton,
        CBox,
        CFormControl, 
        CFormLabel, 
        CInput,
        CButton,
        PierModelFieldOption,
        CPopover,
        CPopoverTrigger,
        CPopoverContent,
        CPopoverHeader,
        CPopoverBody,
        CPopoverArrow,
        CPopoverCloseButton,
    }
}
</script>