<template>
    <div class="ehxd_directory_settings">
        <div class="ehxd_header">
            <h1>Settings</h1>
        </div>

        <div class="ehxd_shortcode_section">
            <div class="ehxd_left">
                <h2>Directory Listing Shortcode</h2>
            </div>
            <div class="ehxd_right">
                <h2>[ehx_directorist_listings]</h2>
                <p>Insert this shortcode on any page or post to display the directory listing. Simply copy and paste it
                    into the content editor. Available parameters:</p>
            </div>

        </div>
    </div>
</template>
<script>

</script>

<style scoped lang="scss">
.ehxd_directory_settings {
    padding: 30px;
    background: #F8F9FC;
    height: 100vh;

    .ehxd_header {

        h1 {
            color: #333;
            font-size: 16px;
            margin: 0;
        }
    }

    .ehxd_shortcode_section {
        margin-top: 30px;
        border-radius: 8px;
        background: #fff;
        padding: 20px;
        display: flex;
        gap: 30px;
        justify-content: space-between;

        .ehxd_left {
            flex-basis: 35%;

            h2 {
                margin: 0;
                color: #333;
                font-size: 14px;

            }
        }

        .ehxd_right {
            h2 {
                margin: 0;
                background: #e7e9ee;
                font-size: 14px;
                padding: 3px 7px;
                width: 23%;
            }

            p {
                padding-top: 5px;
                margin: 0;

            }
        }
    }
}
</style>