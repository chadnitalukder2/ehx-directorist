<template>
    <div class="pair_input_container">
        <div v-for="(option, index) in pair" :key="index" class="pair_input_wrapper">
            <el-form-item prop="name">
                <label>Name</label>
                <el-input v-model="option.name" placeholder="Name"></el-input>
            </el-form-item>
            <el-form-item prop="value">
                <label>Value</label>
                <el-input v-model="option.value" placeholder="Value"></el-input>
            </el-form-item>
            <div class="pair_actions">
                <Icon v-if="pair?.length > 1" :icon="'ehxd-delete'" @click="removePair(index)" />
                <Icon v-if="pair?.length - 1 == index" :icon="'ehxd-plus'" @click="addPair(index)" />
            </div>
        </div>
    </div>
</template>

<script>
import Icon from './Icons/AppIcon.vue';
export default {
    components: {
        Icon
    },
    props: {
        pair: {
            type: Object,
            default: () => [{ name: '', value: '' }]
        }
    },
    methods: {
        addPair(index) {
            console.log('addPair', index);
            this.pair.splice(index + 1, 0, { name: '', value: '' });
        },
        removePair(index) {
            this.pair.splice(index, 1);
        }
    }
};
</script>
<style scoped lang="scss">
.pair_input_container {
    padding: 10px 20px;
    border: 1px solid #D9DADC;
    border-radius: 8px;
    margin-bottom: 10px;
    .pair_input_wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;

        .pair_actions {
            display: flex;
            gap: 10px;
            width: 160px;
        }
    }
}
</style>