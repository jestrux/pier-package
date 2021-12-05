export default {
    component: 'ProfileCard',
    options: {
        image: {
            label: "Picture",
            type: "dynamic",
            accepts: 'image',
            defaultValue: "https://images.unsplash.com/photo-1553804194-fb1475b509b5?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ"
        },
        name: {
            label: "Name",
            type: "dynamic",
            accepts: 'name, string',
            defaultValue: "Dewanda Raisif"
        },
        bio: {
            label: "Bio",
            type: "dynamic",
            accepts: 'long text',
            defaultValue: "Experienced developer working on some cool stuff @Microsoft on Azure."
        },
        instagram: {
            label: "Instagram Profile",
            type: "dynamic",
            accepts: 'link',
            optional: true,
            defaultValue: "https://instagram.com"
        },
        twitter: {
            label: "Twitter Profile",
            type: "dynamic",
            accepts: 'link',
            optional: true,
            defaultValue: "https://twitter.com"
        },
        linkedin: {
            label: "LinkedIn Profile",
            type: "dynamic",
            accepts: 'link',
            optional: true,
            defaultValue: "https://linkedin.com"
        },
        status: {
            label: "User Status",
            type: "dynamic",
            accepts: 'status',
            optional: true,
            defaultValue: "Verified"
        },
    }
}