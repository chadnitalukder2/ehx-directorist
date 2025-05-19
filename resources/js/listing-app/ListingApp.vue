
<template>
    <div class="ehxd_listing_app_container">
        <div class="ehxd-container">
            <!-- Mobile filter toggle button -->
            <button class="ehxd-filter-toggle" @click="showMobileFilters = !showMobileFilters">
                <span v-if="!showMobileFilters">Filters</span>
                <span v-else>Hide Filters</span>
            </button>
            
            <!-- Sidebar -->
            <div class="ehxd-search-sidebar" :class="{ 'ehxd-mobile-filters-active': showMobileFilters }">
                <div class="ehxd-search-container">
                    <!-- Mobile close button -->
                    <div class="ehxd-mobile-close" @click="showMobileFilters = false">
                        <span>×</span>
                    </div>
                    
                    <h3 style="margin-bottom: 0px;" class="ehxd-features-heading">Filter by Address</h3>
                    <GoogleMap :initial-address="address.address" :initial-latitude="address.latitude"
                        :initial-longitude="address.longitude" :showMap="false"
                        @update:address="address.address = $event" @update:latitude="address.latitude = $event"
                        @update:longitude="address.longitude = $event" @update:city="address.city = $event"
                        @update:postalCode="address.postal_code = $event" />

                    <div class="ehxd-radius-slider-container" v-if="address.latitude && address.longitude">
                        <div class="ehxd-radius-label">Within <strong>{{ radius }}</strong> km </div>
                        <input type="range" min="0" max="100" v-model="radius" class="ehxd-radius-slider" />
                    </div>

                    <!-- Category Filters -->
                    <div class="ehxd-features-filter" style="border-bottom: 1px solid #e0e0e0;">
                        <h3 class="ehxd-features-heading">Filter by Category</h3>
                        <div class="ehxd-feature-list">
                            <div v-for="(category, index) in categories" :key="category.id" class="ehxd-feature-item">
                                <input type="checkbox" :id="'category-' + index" v-model="category.selected"
                                    class="ehxd-feature-checkbox" />
                                <label :for="'category-' + index" class="ehxd-feature-label">{{ category.name }}</label>
                            </div>
                        </div>
                    </div>

                    <!-- Tag Filters -->
                    <div class="ehxd-features-filter">
                        <h3 class="ehxd-features-heading">Filter by Tag</h3>
                        <div class="ehxd-feature-list">
                            <div v-for="(tag, index) in tags" :key="tag.id" class="ehxd-feature-item">
                                <input type="checkbox" :id="'tag-' + index" v-model="tag.selected"
                                    class="ehxd-feature-checkbox" />
                                <label :for="'tag-' + index" class="ehxd-feature-label">{{ tag.name }}</label>
                            </div>
                        </div>
                    </div>

                    <!-- Apply filters button for mobile -->
                    <button class="ehxd-apply-filters" @click="showMobileFilters = false">
                        Apply Filters
                    </button>
                </div>
            </div>

            <!-- Listings -->
            <div class="exhd_listing_wrapper" v-loading="loading">
                <div class="ehxd-listing-grid">
                    <div v-for="listing in listings" :key="listing.id" class="ehxd-listing-card">
                        <div class="ehxd-listing-header">
                            <div class="ehxd-profile-image-container">
                                <img :src="listing.logo" alt="img" class="ehxd-profile-image" />
                            </div>
                            <div class="ehxd-listing-info">
                                <h3 class="ehxd-listing-name">{{ listing.name }}</h3>
                                <div class="ehxd-section">
                                    <p class="ehxd-description-text">{{ truncateWords(listing.short_description, 24) }}</p>
                                </div>

                                <div class="ehxd_category">
                                    <p class="ehxd-section-title">Categories: </p>
                                    <div class="ehxd-skills-container">
                                        <span v-for="category in listing.categories" :key="category.id"
                                            class="ehxd-skill-category">
                                            {{ category.name }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ehxd_listing_footer">
                                    <p class="ehxd-listing-location">
                                        <el-icon class="address-icon">
                                            <Location />
                                        </el-icon>
                                        {{ listing.address }}
                                    </p>

                                    <div class="ehxd-listing-view-btn">
                                        <a :href="listing.post_url">
                                            <button class="ehxd-invite-button">View Details</button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="ehxd_footer">
                    <div class="ehxd_footer_page">
                        <p>Page {{ currentPage }} of {{ last_page }}</p>
                    </div>
                    <el-pagination class="ehxd_pagination" v-model:current-page="currentPage"
                        v-model:page-size="pageSize" :page-sizes="[1, 10, 20, 30, 40]" large
                        :disabled="total_list <= pageSize" background layout="total, sizes, prev, pager, next"
                        :total="+total_list" />
                </div>
            </div>
            
            <!-- Overlay for mobile filters -->
            <div class="ehxd-overlay" v-if="showMobileFilters" @click="showMobileFilters = false"></div>
        </div>
    </div>
</template>

<script>
import GoogleMap from "../admin/components/GoogleMapInput.vue";
import axios from "axios";
export default {
    components: {
        GoogleMap
    },
    data() {
        return {
            address: {
                address: '',
                latitude: '',
                longitude: '',
                city: '',
                postal_code: ''
            },
            listings: [],
            categories: [],
            tags: [],
            search: '',
            list: {},
            total_list: 0,
            currentPage: 1,
            last_page: 1,
            pageSize: 10,
            radius: 30,
            loading: false,
            nonce: window.EhxDirectoristData.nonce,
            rest_api: window.EhxDirectoristData.rest_api,
            showMobileFilters: false
        };
    },
    watch: {
        radius() {
            this.currentPage = 1;
            this.getAllListings();
        },
        address: {
            handler() {
                this.currentPage = 1;
                this.getAllListings();
            },
            deep: true
        },
        currentPage() {
            this.getAllListings();
        },
        pageSize() {
            this.currentPage = 1;
            this.getAllListings();
        },
        search: {
            handler() {
                this.currentPage = 1;
                this.getAllListings();
            },
            immediate: false
        },
        categories: {
            handler() {
                this.currentPage = 1;
                this.getAllListings();
            },
            deep: true
        },
        tags: {
            handler() {
                this.currentPage = 1;
                this.getAllListings();
            },
            deep: true
        }
    },

    methods: {
        truncateWords(text, maxWords = 200) {
            if (!text) return '';
            const words = text.trim().split(/\s+/);
            if (words.length <= maxWords) return text;
            return words.slice(0, maxWords).join(' ') + '...';
        },

        async getAllListings() {
            this.loading = true;
            const selectedCategoryIds = this.categories
                .filter(cat => cat.selected)
                .map(cat => cat.id);

            const selectedTagIds = this.tags
                .filter(tag => tag.selected)
                .map(tag => tag.id);

            try {
                const response = await axios.get(`${this.rest_api}/getAllListings`, {
                    params: {
                        page: this.currentPage,
                        limit: this.pageSize,
                        search: this.search || '',
                        categories: selectedCategoryIds.join(','),
                        tags: selectedTagIds.join(','),
                        radius: this.radius,
                        latitude: this.address.latitude,
                        longitude: this.address.longitude
                    },
                    headers: {
                        'X-WP-Nonce': this.nonce
                    }
                });
                this.listings = response?.data?.listings_data;
                this.total_list = response?.data?.total || 0;
                this.last_page = response?.data?.last_page || 1;
                this.loading = false;
            } catch (error) {
                this.loading = false;
                console.error('Error fetching listings:', error);
            }
        },
        async getAllCategories() {
            this.loading = true;
            try {
                const response = await axios.get(`${this.rest_api}/getAllCategories`, {
                    params: {},
                    headers: {
                        'X-WP-Nonce': this.nonce
                    }
                });
                this.categories = response?.data?.categories_data;
                this.loading = false;
            } catch (error) {
                this.loading = false;
                console.error('Error fetching categories:', error);
            }
        },
        async getAllTag() {
            this.loading = true;
            try {
                const response = await axios.get(`${this.rest_api}/getAllTag`, {
                    params: {},
                    headers: {
                        'X-WP-Nonce': this.nonce
                    }
                });
                this.tags = response?.data?.tags_data;
                this.loading = false;
            } catch (error) {
                this.loading = false;
                console.error('Error fetching tags:', error);
            }
        },
    },

    mounted() {
        this.getAllListings();
        this.getAllCategories();
        this.getAllTag();
    }
};
</script>

<style lang="scss" scoped>
.ehxd-container {
    display: flex;
    font-family: Arial, sans-serif;
    max-width: 1200px;
    margin: 0 auto;
    gap: 30px;
    padding: 20px;
    position: relative;
}

/* Mobile filter toggle button */
.ehxd-filter-toggle {
    display: none;
    margin-bottom: 15px;
    background-color: #000;
    color: white;
    border: none;
    border-radius: 0px 6px 6px 0px;
    font-size: 16px;
    cursor: pointer;
    left: 0;
    z-index: 9;
    margin: -54px 0px;
    padding: 12px 30px;
    position: fixed;
}

/* Overlay for mobile filters */
.ehxd-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 998;
}

/* Mobile close button */
.ehxd-mobile-close {
    display: none;
    text-align: right;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    margin-bottom: 10px;
}

/* Apply filters button for mobile */
.ehxd-apply-filters {
    display: none;
    width: 100%;
    padding: 12px;
    background-color: #000;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 20px;
}

//=================================
.ehxd-search-sidebar {
    flex-basis: 30%;
    border-radius: 6px;
    background-color: #fff;
    padding: 30px;
    font-family: Arial, sans-serif;
}

.ehxd-search-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.ehxd-search-input-wrapper,
.ehxd-dropdown-wrapper,
.ehxd-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    padding-bottom: 25px;
    border-bottom: 1px solid rgb(224, 224, 224);

    .ehxd_icon {
        position: absolute;
        position: absolute;
        color: #666666;
        font-weight: 100;
        padding: 1px 0px 0px 8px;
    }
}

.ehxd-icon {
    position: absolute;
    left: 10px;
    color: #777;
    font-size: 16px;
}

.ehxd-features-filter {
    padding-bottom: 25px;
    padding-top: 10px;
}

.ehxd-search-input,
.ehxd-location-input {
    width: 100%;
    padding: 12px 10px 12px 35px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    font-size: 14px;
    background: #FBFBFB;

    &:focus-visible {
        outline: none;
    }
}

.ehxd-dropdown {
    width: 100%;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 10px 10px 10px 35px;
    cursor: pointer;
    position: relative;
}

.ehxd-dropdown-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ehxd-icon-chevron-down {
    position: absolute;
    right: 10px;
    color: #777;
}

.ehxd-radius-slider-container {
    margin-top: 5px;
    padding-bottom: 25px;
    border-bottom: 1px solid rgb(224, 224, 224);
}

.ehxd-radius-label {
    font-size: 15px;
    margin-bottom: 10px;
    color: #666;
}

.ehxd-radius-slider {
    width: 100%;
    -webkit-appearance: none;
    height: 5px;
    border-radius: 5px;
    background: #e0e0e0;
    outline: none;
}

.ehxd-radius-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    background: #000;
    cursor: pointer;
}

.ehxd-features-heading {
    font-size: 16px;
    font-weight: bold;
    margin: 0 0 15px 0;
    color: #333;
}

.ehxd-feature-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.ehxd-feature-item {
    display: flex;
    align-items: center;
}

.ehxd-feature-checkbox {
    margin-right: 10px;
    appearance: none;
    width: 18px;
    height: 18px;
    border: 1px solid #ddd;
    border-radius: 3px;
    position: relative;
    cursor: pointer;
}

.ehxd-feature-checkbox:checked {
    background-color: #000;
    border-color: #000;
}

.ehxd-feature-checkbox:checked::after {
    content: "";
    position: absolute;
    top: 3px;
    left: 6px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.ehxd-feature-label {
    font-size: 14px;
    color: #555;
}

.ehxd-search-button {
    background-color: #000;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 12px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease-in-out;
}

.ehxd-search-button:hover {
    background-color: #000;
}

.ehxd-search-results-info {
    font-size: 13px;
    color: #666;
    text-align: center;
    line-height: 1.5;
}

.ehxd-result-count {
    font-weight: bold;
}

.ehxd-reset-link {
    color: #000;
    text-decoration: none;
}

.ehxd-reset-link:hover {
    text-decoration: underline;
}




//=================================================
.exhd_listing_wrapper {
    flex-basis: 67%;

    .ehxd-results-header {
        display: flex;
        justify-content: space-between;
        text-align: center;
        align-items: center;
        padding: 15px 0PX 35PX 0px;

        .ehxd-results-count {
            margin: 0;
            font-weight: 500;
            color: #11161F;
            font-size: 16px;
        }

        .ehxd-sort-select {
            font-size: 16px;
            color: #787878;
            border-radius: 6px;
            background-color: #FBFBFB;
            border: 1px solid #e2dfeb;
            padding: 10px 12px;

            &:focus-visible {
                outline: none;

            }
        }
    }
}

//===============================
.ehxd-listing-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 30px;
}

.ehxd-listing-card {
    border: 1px solid #ddd;
    background-color: white;
    border-radius: 10px;
    padding: 20px;
    display: flex;
    flex-direction: column;
}

.ehxd-listing-header {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}



.ehxd-profile-image {
    width: 100px;
    height: 96px;
    border-radius: 8px;
    object-fit: cover;
}

.ehxd-listing-info {
    width: 100%;
    flex-basis: 100%;
}

.ehxd-listing-name {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0;
    margin-bottom: 4px;
}

.ehxd-listing-location {
    margin-top: 20px;
    display: flex;
    align-items: center;
    font-size: 14px;
    color: #333;
    margin: 0;
    flex-basis: 60%;

    .address-icon {
        font-size: 15px;
        color: #000;
        margin-right: 5px;
        font-weight: 600;
    }
}


.ehxd-section {
    flex-basis: 25%;
}

.ehxd-section-title {
    font-size: 16px;
    color: #333;
    text-transform: capitalize;
    margin: 0;
}

.ehxd-description-text {
    font-size: 14px;
    color: #666;
    line-height: 1.5;
    margin: 0;
}

.ehxd_category {
    display: flex;
    gap: 10px;
    text-align: center;
    align-items: center;
    padding: 18px 0px 12px 0px;

    .ehxd-skills-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
}


.ehxd-skill-category {
    padding: 5px 15px;
    border: 1px solid #ddd;
    border-radius: 20px;
    font-size: 13px;
    background: #CEECDF;
    color: #256a4c;
}

.ehxd-more-tag {
    background-color: white;
    color: #555;
}

.ehxd_listing_footer {
    display: flex;
    padding-top: 12px;
    gap: 30px;
    justify-content: space-between;
    align-items: center;
    text-align: center;

    .ehxd-listing-view-btn {
        flex-basis: 30%;
    }
}



.ehxd-stat {
    display: flex;
    align-items: center;
    font-size: 14px;
    color: #555;
}

.ehxd-icon {
    margin-right: 8px;
    color: #666;
}



.ehxd-invite-button {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    background-color: #000;
    color: #fff;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
}

.ehxd-invite-button:hover {
    background-color: rgb(0 0 0 / 82%);
}

//=========================================
.ehxd_footer {
    display: flex;
    padding: 30px 0px 0px 0px;
    justify-content: space-between;
    align-items: center;

    .ehxd_footer_page {
        p {
            margin: 0;
            font-size: 15px;
            color: #666666;
        }
    }
}

/* Media Queries for Responsive Design */
@media screen and (max-width: 990px) {
    .ehxd-container {
        flex-direction: column;
        padding: 15px;
        gap: 20px;
    }
    
    .ehxd-search-sidebar {
        flex-basis: 100%;
        padding: 20px;
    }
    
    .exhd_listing_wrapper {
        flex-basis: 100%;
    }
}

@media screen and (max-width: 1089px) {
    .ehxd-filter-toggle {
        display: block;
    }
    
    .ehxd-search-sidebar {
        position: fixed;
        top: 0;
        left: -100%;
        width: 80%;
        height: 100vh;
        max-width: 350px;
        z-index: 999;
        transition: left 0.3s ease;
        overflow-y: auto;
        padding: 20px;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }
    
    .ehxd-mobile-filters-active {
        left: 0;
    }
    
    .ehxd-mobile-close {
        display: block;
    }
    
    .ehxd-apply-filters {
        display: block;
    }
    
    .showMobileFilters .ehxd-overlay {
        display: block;
    }
    
    .ehxd-listing-header {
        flex-direction: column;
    }
    
    .ehxd-profile-image-container {
        text-align: center;
        margin: 0 auto;
        margin-bottom: 15px;
    }
    
    .ehxd-profile-image {
        width: 120px;
        height: 120px;
    }
    
    .ehxd_category {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .ehxd_listing_footer {
        display: block;
        flex-direction: column;
        gap: 15px;
    }
    
    .ehxd-listing-location{
        padding-top: 0px;
    }

    .ehxd-listing-view-btn {
        padding-top: 18px;
        width: 135px;
        margin: 0 auto;
        .ehxd-invite-button{
     
        }
    }
    
    .ehxd_footer {
        flex-direction: column;
        gap: 15px;
        align-items: center;
    }
    
    .ehxd_pagination {
        .el-pagination__sizes {
            display: none !important;
        }
    }
}

@media screen and (max-width: 480px) {
    .ehxd-container {
        padding: 10px;
    }
    
    .ehxd-listing-card {
        padding: 15px;
    }
    
    .ehxd-search-sidebar {
        width: 90%;
        padding: 15px;
    }
}
</style>
