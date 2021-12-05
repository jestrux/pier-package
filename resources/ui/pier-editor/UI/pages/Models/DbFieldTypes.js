export default [
  {
    label: 'Short Text',
    value: 'string',
    placeholder: 'E.g. title'
  },
  {
    label: 'Long Text',
    value: 'long text',
    placeholder: 'E.g. description',
    options: {
      wysiwyg: {
        label: "Is wysiwyg field",
        type: "toggle",
        defaultValue: false
      }
    }
  },
  {
    label: 'Number',
    value: 'number',
    placeholder: 'E.g. points'
  },
  {
    label: 'Name',
    value: 'name',
    placeholder: 'E.g. full_name'
  },
  {
    label: 'Email',
    value: 'email',
    placeholder: 'E.g. email'
  },
  {
    label: 'Password',
    value: 'password',
    placeholder: 'E.g. password'
  },
  {
    label: 'Phone',
    value: 'phone',
    placeholder: 'E.g. phoneNo'
  },
  {
    label: 'Date',
    value: 'date',
    placeholder: 'E.g. dob',
    options: {
      // type: {
      //   label: "Type of date",
      //   type: "radio",
      //   choices: ["Single", "Range"],
      //   defaultValue: "Single"
      // },
      includeTime: {
        label: "Include Time",
        type: "toggle",
        defaultValue: false
      }
    }
  },
  {
    label: 'Image',
    value: 'image',
    placeholder: 'E.g. profile_picture',
    options: {
      face: {
        label: "Is a picture of person",
        type: "toggle",
        defaultValue: false
      }
    }
  },
  {
    label: 'Video',
    value: 'video',
    placeholder: 'E.g. youtube_url'
  },
  {
    label: 'File',
    value: 'file',
    placeholder: 'E.g. attachment'
  },
  {
    label: 'Link',
    value: 'link',
    placeholder: 'E.g. rsvp_link'
  },
  {
    label: 'Location',
    value: 'location',
    placeholder: 'E.g. location',
    options: {
      countries: {
        label: "Supported Countries",
        placeholder: 'E.g. us,uk,tz',
        hint: "Leave empty to support all countries",
        defaultValue: ""
      }
    }
  },
  {
    label: 'Rating',
    value: 'rating',
    placeholder: 'E.g. rating',
    options: {
      outOf: {
        label: "Rating out of",
        type: "radio",
        choices: [
          { label: "FIVE", value: "5" },
          { label: "TEN", value: "10" },
        ],
        defaultValue: "5"
      }
    }
  },
  {
    label: 'Switch',
    value: 'boolean',
    placeholder: 'E.g. in_stock'
  },
  {
    label: 'Color',
    value: 'color',
    // placeholder: 'E.g. in_stock'
  },
];