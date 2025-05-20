<template>
  <div class="ehxd_wrapper">
    <div class="">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item :to="{ path: '/directory-listing' }">Directory listing</el-breadcrumb-item>
        <el-breadcrumb-item>Add listings</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="ehxd_form_wrapper">
      <el-form ref="ListForm" :model="localList" :rules="rules" label-position="top">
        <div class="ehxd_input_item">
          <el-form-item label="Title" prop="name">
            <el-input v-model="localList.name" placeholder="Enter title" />
          </el-form-item>
        </div>

        <div class="ehxd_input_item">
          <el-form-item label="Email" prop="email">
            <el-input v-model="localList.email" placeholder="demo@example.com" />
          </el-form-item>
        </div>

        <div class="ehxd_input_item">
          <el-form-item label="Phone" prop="phone">
            <el-input v-model="localList.phone" placeholder="Phone number" />
          </el-form-item>
        </div>

        <div class="ehxd_input_item">
          <el-form-item label="Website" prop="website_url">
            <el-input v-model="localList.website_url" placeholder="https://example.com/" />
          </el-form-item>
        </div>

        <div class="ehxd_input_item">
          <el-form-item label="Category Name" prop="category_id">
            <el-select multiple class="erm_input" v-model="localList.category_id" placeholder="Category Name"
              size="large" style="width: 100%">
              <el-option v-for="category in categories" :key="category.id" :label="category.name"
                :value="category.id" />
            </el-select>
          </el-form-item>
        </div>

        <div class="ehxd_input_item">
          <el-form-item label="Tag Name" prop="tag_id">
            <el-select multiple class="erm_input" v-model="localList.tag_id" placeholder="Tag Name" size="large"
              style="width: 100%">
              <el-option v-for="tag in tags" :key="tag.id" :label="tag.name" :value="tag.id" />
            </el-select>
          </el-form-item>
        </div>

        <el-form-item label="Address" prop="address">
          <GoogleMapForAddListing :initial-address="localList.address" :initial-latitude="localList.latitude"
            :initial-longitude="localList.longitude" @update:address="localList.address = $event"
            @update:latitude="localList.latitude = $event" @update:longitude="localList.longitude = $event"
            @update:city="localList.city = $event" @update:postalCode="localList.postal_code = $event" />
        </el-form-item>

        <div class="ehxd_image_wrapper">
          <div class="ehxd_image_item">
            <el-form-item label="Feature Image" prop="image">
              <AppFileUpload v-model:selectedFile="localList.image" btnTitle="Add Media" />
            </el-form-item>
          </div>
          <div class="ehxd_image_item">
            <el-form-item label="Logo" prop="logo">
              <AppFileUploadLogo v-model:selectedFile="localList.logo" btnTitle="Add Media" />
            </el-form-item>
          </div>
        </div>

        <el-form-item label="Brief Description" prop="short_description">
          <el-input v-model="localList.short_description" type="textarea" placeholder="Enter brief description" />
        </el-form-item>

        <el-form-item label="Description" prop="description">
          <el-input v-model="localList.description" type="textarea" placeholder="Enter description" rows="8" />
          <!-- <WpEditor  ref="descriptionEditor" v-model="localList.description" /> -->
        </el-form-item>

        <!-- social link section start -->
        <div class="ehxd_input_item">
          <el-form-item label="Social Media Links">
            <div v-for="(link, index) in socialLinks" :key="index" class="social-link-row">
              <el-select v-model="link.icon" placeholder="Icon" style="width: 200px;">
                <el-option v-for="icon in availableIcons" :key="icon.value" :label="icon.label" :value="icon.value">
                  <i :class="`fab fa-${icon.value}`" style="margin-right: 5px;"></i> {{ icon.label }}
                </el-option>
              </el-select>

              <el-input v-model="link.url" placeholder="https://..." style="margin-right: 8px;" />

              <div class="ehxd_delete_icon" @click="removeSocialLink(index)">
                <el-icon>
                  <Delete />
                </el-icon>
              </div>
            </div>

            <el-button icon="Plus" @click="addSocialLink" style="margin-top: 10px;">
              Add Social Link
            </el-button>
          </el-form-item>
        </div>
        <!-- social link section end -->

        <!-- custom fields section -->
        <div class="custom_field_list_wrapper">
          <div class="custom_field_list_item" v-for="(field, index) in customFields" :key="field.key">
            <el-form-item label="Field Label">
              <el-input :id="field.key + '_label'" v-model="customFields[index].label" />
            </el-form-item>
            <el-form-item label="Field Value">
              <el-input :type="field.type" :id="field.key" v-model="customFields[index].value" />
            </el-form-item>
            <div class="ehxd_delete_icon" @click="removeCustomField(index)">
              <el-icon>
                <Delete />
              </el-icon>
            </div>
          </div>
        </div>

        <div style="margin-bottom: 20px;" class="custom_fields_button">
          <p>Select Custom Fields</p>
          <el-button size="large" v-for="field in form_fields" :key="field.key" @click="addCustomField(field)">
            {{ field.field }}
          </el-button>
        </div>

        <el-form-item style="padding-top: 20px;">
          <el-button type="primary" size="large" @click="submitListForm">
            Save Listing
          </el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Icon from "../../components/Icons/AppIcon.vue";
import GoogleMapForAddListing from "../../components/GoogleMapForAddListing.vue";
import AppFileUpload from "../../components/AppFileUpload.vue";
import AppFileUploadLogo from "../../components/AppFileUploadLogo.vue";
import WpEditor from "../../components/WpEditor.vue";
import { Delete } from '@element-plus/icons-vue';

export default {
  components: {
    Icon,
    GoogleMapForAddListing,
    AppFileUpload,
    AppFileUploadLogo,
    WpEditor,
    Delete
  },
  data() {
    return {
      localList: {
        name: "",
        email: "",
        phone: "",
        address: "",
        website_url: "",
        short_description: "",
        description: "",
        latitude: "",
        longitude: "",
        postal_code: "",
        city: "",
        logo: "",
        image: "",
        category_id: [],
        tag_id: [],
        directory_builder_id: 1,
      },
      tags: [],
      categories: [],
      socialLinks: [
        { icon: 'facebook', url: '' },
      ],
      availableIcons: [
        { label: "Facebook", value: "facebook" },
        { label: "Instagram", value: "instagram" },
        { label: "LinkedIn", value: "linkedin" },
        { label: "YouTube", value: "youtube" },
        { label: "TikTok", value: "tiktok" },
        { label: "Pinterest", value: "pinterest" },
        { label: "Snapchat", value: "snapchat" },
        { label: "WhatsApp", value: "whatsapp" },
        { label: "Telegram", value: "telegram" },
        { label: "Reddit", value: "reddit" },
        { label: "Discord", value: "discord" },
        { label: "GitHub", value: "github" },
        { label: "Twitter", value: "twitter" },
        { label: "Dribbble", value: "dribbble" },
        { label: "Behance", value: "behance" },
        { label: "Medium", value: "medium" },
        { label: "Twitch", value: "twitch" },
        { label: "Spotify", value: "spotify" },
        { label: "Stack Overflow", value: "stack-overflow" },
        { label: "Tumblr", value: "tumblr" },
        { label: "Vimeo", value: "vimeo" },
        { label: "SoundCloud", value: "soundcloud" },
        { label: "Flickr", value: "flickr" },
        { label: "WeChat", value: "wechat" },
        { label: "WordPress", value: "wordpress" },
        { label: "Line", value: "line" },
        { label: "Quora", value: "quora" },
        { label: "Goodreads", value: "goodreads" },
        { label: "Skype", value: "skype" },
        { label: "Viber", value: "viber" },
      ],

      customFields: [],
      nonce: window.EhxDirectoristData.nonce,
      rest_api: window.EhxDirectoristData.rest_api,
      form_fields: [
        { label: "Text Field", key: "text_field", type: "input", value: "", field: 'Text' },
        { label: "Number Field", key: "number_field", type: "number", value: "", field: 'Number' },
        { label: "Email Field", key: "email_field", type: "input", value: "", field: 'Email' },
        { label: "Textarea Field", key: "textarea_field", type: "textarea", value: "", field: 'Textarea' },
      ]
    };
  },
  computed: {
    rules() {
      return {
        name: [{ required: true, message: "Please input the title", trigger: "blur" }],
        email: [
          { required: true, message: "Please input the email", trigger: "blur" },
          { type: "email", message: "Please input a valid email address", trigger: ["blur", "change"] },
        ],
        phone: [{ required: true, message: "Please input the phone number", trigger: "blur" }],
        website_url: [{ required: true, message: "Please input the website URL", trigger: "blur" }],
        category_id: [{ required: true, message: "Please select a category", trigger: "change" }],
        tag_id: [{ required: true, message: "Please select a tag", trigger: "change" }],
        address: [{ required: true, message: "Please input the address", trigger: "blur" }],
        logo: [{ required: true, message: "Please upload a logo", trigger: "change" }],
        short_description: [
          { required: true, message: "Please input the brief description", trigger: "blur" },
          {
            validator: (rule, value, callback) => {
              if (value.length > 255) {
                callback(new Error("Brief description must not exceed 255 characters."));
              } else {
                callback();
              }
            },
            trigger: "blur"
          }
        ],
      };
    }
  },
  methods: {
    async getAllCategories() {
      try {
        const res = await axios.get(`${this.rest_api}/getAllCategories`, {
          headers: { 'X-WP-Nonce': this.nonce }
        });
        this.categories = res?.data?.categories_data || [];
      } catch (err) {
        console.error('Failed to load categories:', err);
      }
    },
    async getAllTag() {
      try {
        const res = await axios.get(`${this.rest_api}/getAllTag`, {
          headers: { 'X-WP-Nonce': this.nonce }
        });
        this.tags = res?.data?.tags_data || [];
      } catch (err) {
        console.error('Failed to load tags:', err);
      }
    },
    addSocialLink() {
      this.socialLinks.push({ label: '', icon: '', url: '' });
    },
    removeSocialLink(index) {
      this.socialLinks.splice(index, 1);
    },
    addCustomField(field) {
      const baseKey = field.key;
      let index = 0;
      let uniqueKey = baseKey;
      while (this.customFields.find(f => f.key === uniqueKey)) {
        uniqueKey = `${baseKey}_${++index}`;
      }
      this.customFields.push({ ...field, key: uniqueKey });
    },
    removeCustomField(index) {
      this.customFields.splice(index, 1);
    },
    async submitListForm() {
      this.$refs.ListForm.validate(async (valid) => {
        if (!valid) return;

        this.localList.social_links = this.socialLinks;
        this.localList.meta = this.customFields;

        try {
          const res = await axios.post(`${this.rest_api}/postDirectoryListing`, this.localList, {
            headers: {
              "Content-Type": "application/json",
              "X-WP-Nonce": this.nonce,
            },
          });

          if (res.data.success === true) {
            this.$notify({
              title: "Success",
              message: "Directory Listing created successfully",
              type: "success",
            });

            // Reset form
            this.localList = {
              name: "",
              email: "",
              phone: "",
              address: "",
              website_url: "",
              short_description: "",
              description: "",
              latitude: "",
              longitude: "",
              postal_code: "",
              city: "",
              logo: "",
              image: "",
              category_id: [],
              tag_id: [],
              directory_builder_id: 1,
            };
            this.customFields = [];
            this.socialLinks = [];

            // Optional: redirect
            // this.$router.push('/directory-listing');
          } else {
            this.$notify({
              title: "Error",
              message: "Failed to create directory listing",
              type: "error",
            });
          }
        } catch (err) {
          console.error("Submission error:", err);
          this.$notify({
            title: "Error",
            message: "An unexpected error occurred. Please try again.",
            type: "error",
          });
        }
      });
    }
  },
  mounted() {
    this.getAllCategories();
    this.getAllTag();
  }
};
</script>

<style lang="scss" scoped>
.ehxd_wrapper {
  padding: 20px 35px;
  background-color: #F8F9FC;
  font-family: Inter;

  .custom_field_list_item {
    display: flex;
    align-items: center;
    gap: 20px;

    .el-form-item {
      flex: 1;
    }
  }

  .ehxd-header-section {
    align-items: center;
    background-color: #fff;
    display: flex;
    border-radius: 10px;
    padding: 12px 30px;
    justify-content: space-between;

    .ehxd_title {
      .table-title {
        margin: 0;
        color: #2F3448;
        font-family: Inter;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: 24px;
      }

      .table-short-dsc {
        padding-top: 4px;
        margin: 0px;
        font-size: 12px;
        color: #3c434a;
        line-height: 1.5;
      }
    }

    .ehxd_header_btn {
      .ltm_button {
        padding: 0px 10px;
        font-size: 13px;
        font-weight: 500;
        line-height: 24px;
        border-radius: 8px;
        height: 35px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
      }
    }
  }

  .ehxd_form_wrapper {
    margin: 24px 105px;
    border-radius: 8px;
    padding: 30px 60px;
    background: #fff;




  }

  .el-icon {
    font-size: 16px;
    color: #3c434a;
    cursor: pointer;
    transition: all 0.2s ease-in-out;

    &:hover {
      color: #ff4d4f;
    }
  }

  .custom_fields_button {
    p {
      font-size: 14px;
    }
  }

  .el-button {
  transition: all 0.3s ease-in-out;
    &:hover {
      background-color: #1A1B1C;
      color: #fff;
    }
  }
}

.ehxd_image_wrapper {
  display: flex;
  justify-content: space-between;
  gap: 30px;
  margin-bottom: 20px;

  .ehxd_image_item {
    flex: 1;

    .ehxd_image_item {
      margin-bottom: 0px;
    }
  }
}

.social-link-row {
  padding: 0px 0px 12px;
  display: flex;
  text-align: center;
  align-items: center;
  gap: 10px;
}
</style>