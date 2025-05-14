<template>
    <div class="ehxd-container">
        <div class="ehxd-search-sidebar">
            <div class="ehxd-search-container">

                <div class="ehxd-input-wrapper">
                    <span class="dashicons dashicons-search ehxd_icon"></span>
                    <input type="text" placeholder="Location" class="ehxd-location-input">
                </div>

                <div class="ehxd-radius-slider-container">
                    <div class="ehxd-radius-label">Within <strong> {{ radius }}</strong> miles</div>
                    <input type="range" min="0" max="100" v-model="radius" class="ehxd-radius-slider">
                </div>

                <div class="ehxd-features-filter">
                    <h3 class="ehxd-features-heading">Filter by Category</h3>

                    <div class="ehxd-feature-list">
                        <div v-for="(feature, index) in features" :key="index" class="ehxd-feature-item">
                            <input type="checkbox" :id="'feature-' + index" v-model="feature.selected"
                                class="ehxd-feature-checkbox">
                            <label :for="'feature-' + index" class="ehxd-feature-label">{{ feature.name }}</label>
                        </div>
                    </div>
                </div>

                <div class="ehxd-features-filter">
                    <h3 class="ehxd-features-heading">Filter by Tag</h3>

                    <div class="ehxd-feature-list">
                        <div v-for="(feature, index) in features" :key="index" class="ehxd-feature-item">
                            <input type="checkbox" :id="'feature-' + index" v-model="feature.selected"
                                class="ehxd-feature-checkbox">
                            <label :for="'feature-' + index" class="ehxd-feature-label">{{ feature.name }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="exhd_listing_wrapper">
            <div class="ehxd-results-area">
                <div class="ehxd-results-header">
                    <h2 class="ehxd-results-count">{{ total_List }} Results Found</h2>
                    <div class="ehxd-sort-dropdown">
                        <select class="ehxd-sort-select">
                            <option>Sort by oldest</option>
                            <option>Sort by newest</option>
                            <option>Sort by price: low to high</option>
                            <option>Sort by price: high to low</option>
                            <option>Sort by rating</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="ehxd-freelancer-grid">
                
                <div v-for="listing in listings" class="ehxd-freelancer-card">
                    <div class="ehxd-freelancer-header">
                        <div class="ehxd-profile-image-container">
                            <img :src="listing.image" alt="img" class="ehxd-profile-image" />
                        </div>
                        <div class="ehxd-freelancer-info">
                            <h3 class="ehxd-freelancer-name">{{ listing.name }}</h3>
                            <p class="ehxd-freelancer-location">{{ listing.address }}</p>
                        </div>
                    </div>

                    <div class="ehxd-section">
                        <p class="ehxd-section-title">DESCRIPTION</p>
                        <p class="ehxd-description-text">{{ listing.short_description }}</p>
                    </div>

                    <div class="ehxd-section">
                        <p class="ehxd-section-title">Categories</p>
                        <div class="ehxd-skills-container">
                            <span v-for="category in listing.category_id"
                                class="ehxd-skill-tag">
                                {{ category }}
                            </span>
                            <!-- <span class="ehxd-skill-tag ehxd-more-tag">{{ freelancer.moreSkills }} more</span> -->
                        </div>
                    </div>

                    <div class="ehxd-freelancer-footer">
                        <button class="ehxd-invite-button">View Details</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import axios from "axios";
export default {
  
    data() {
        return {
            listings: [],
            search: '',
            listings: [],
            list: {},
            total_List: 0,
            loading: false,
            currentPage: 1,
            last_page: 1,
            pageSize: 10,
            // freelancers: [
            //     {
            //         name: "Michael Spitz",
            //         location: "Los Angeles, CA",
            //         isAvailable: true,
            //         description: "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accus",
            //         skills: ["Design", "Frontend Developer"],
            //         moreSkills: 2,
            //         projects: 12,
            //         hourlyRate: 80,
            //         image: "../../../assets/images/no-image.jpg"
            //     },
            //     {
            //         name: "Marco Coppeto",
            //         location: "New York, NY",
            //         isAvailable: false,
            //         description: "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accus",
            //         skills: ["Design", "Frontend Developer"],
            //         moreSkills: 2,
            //         projects: 12,
            //         hourlyRate: 80,
            //         image: "https://via.placeholder.com/100"
            //     },
            //     {
            //         name: "Gene Ross",
            //         location: "Los Angeles, CA",
            //         isAvailable: true,
            //         description: "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accus",
            //         skills: ["Design", "Frontend Developer"],
            //         moreSkills: 2,
            //         projects: 12,
            //         hourlyRate: 80,
            //         image: "https://via.placeholder.com/100"
            //     },
            //     {
            //         name: "Michael Spitz",
            //         location: "Los Angeles, CA",
            //         isAvailable: true,
            //         description: "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accus",
            //         skills: ["Design", "Frontend Developer"],
            //         moreSkills: 2,
            //         projects: 12,
            //         hourlyRate: 80,
            //         image: "https://via.placeholder.com/100"
            //     },

            // ],
            searchQuery: '',
            radius: 30,
            features: [
                { name: 'Accepts Credit Cards', selected: false },
                { name: 'Appointment', selected: false },
                { name: 'Auto System', selected: false },
                { name: 'Car Parking', selected: true },
            ],
            
            nonce: window.EhxDirectoristData.nonce,
            rest_api: window.EhxDirectoristData.rest_api,
        };
    },
    methods: {

        async getAllListings() {
            this.loading = true;
            try {
                const response = await axios.get(`${this.rest_api}/getAllListings`, {
                    params: {
                        page: this.currentPage,
                        limit: this.pageSize,
                        search: this.search || '',
                    },
                    headers: {
                        'X-WP-Nonce': this.nonce
                    }
                });
                this.listings = response?.data?.listings_data;
                this.total_List = response?.data?.total || 0;
                this.last_page = response?.data?.last_page || 1;
                this.loading = false;
            } catch (error) {
                this.loading = false;
                console.error('Error fetching couriers:',);
            }
        },

    },

    mounted() {
        this.getAllListings();
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
}

//=================================
.ehxd-search-sidebar {
    flex-basis: 30%;
    border: 1px solid #e0e0e0;
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

    .ehxd_icon {
        position: absolute;
        position: absolute;
        color: #666666;
        font-weight: 100;
        padding: 2px 0px 0px 8px;
    }
}

.ehxd-icon {
    position: absolute;
    left: 10px;
    color: #777;
    font-size: 16px;
}

.ehxd-features-filter {
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 20px;
    padding-top: 10px;
}

.ehxd-search-input,
.ehxd-location-input {
    width: 100%;
    padding: 12px 10px 12px 35px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    font-size: 16px;
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
}

.ehxd-radius-label {
    font-size: 14px;
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
    transition: background-color 0.2s;
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

.ehxd-icon-map-pin::before {
    content: "📍";
}



//=================================================
.exhd_listing_wrapper {
    flex-basis: 67%;
    .ehxd-results-header {
        display: flex;
        justify-content: space-between;
        text-align: center;
        align-items: center;
        padding: 20px 0px;

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
.ehxd-freelancer-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.ehxd-freelancer-card {
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    padding: 20px;
    display: flex;
    flex-direction: column;
}

.ehxd-freelancer-header {
    display: flex;
    align-items: flex-start;
    margin-bottom: 20px;
}

.ehxd-profile-image-container {
    margin-right: 15px;
}

.ehxd-profile-image {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #1e3a8a;
}

.ehxd-freelancer-info {
    flex-grow: 1;
}

.ehxd-freelancer-name {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0;
    margin-bottom: 4px;
}

.ehxd-freelancer-location {
    font-size: 14px;
    color: #666;
    margin: 0;
    margin-bottom: 8px;
}


.ehxd-section {
    margin-bottom: 20px;
}

.ehxd-section-title {
    font-size: 12px;
    color: #999;
    text-transform: uppercase;
    margin: 0 0 5px 0;
}

.ehxd-description-text {
    font-size: 14px;
    color: #666;
    line-height: 1.5;
    margin: 0;
}

.ehxd-skills-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.ehxd-skill-tag {
    padding: 5px 15px;
    border: 1px solid #ddd;
    border-radius: 20px;
    font-size: 13px;
    color: #555;
}

.ehxd-more-tag {
    background-color: white;
    color: #555;
}

.ehxd-freelancer-footer {
    margin-top: auto;
    padding-top: 15px;
    border-top: 1px solid #ddd;
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

.ehxd-icon-project::before {
    content: "📁";
}

.ehxd-icon-money::before {
    content: "💰";
}

.ehxd-invite-button {
    width: 100%;
    padding: 12px;
    background-color: #000;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
}

.ehxd-invite-button:hover {
    background-color: #00000079;
}
</style>