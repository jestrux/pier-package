export default {
    component: 'TaskCard',
    options: {
        status: {
            label: "Status",
            type: "dynamic",
            accepts: 'status',
            optional: true,
            defaultValue: "Complete"
        },
        date: {
            label: "Due On",
            type: "dynamic",
            accepts: 'date',
            optional: true,
            defaultValue: new Date().getFullYear() + "-07-13"
        },
        title: {
            label: "Title",
            type: "dynamic",
            accepts: 'string, long text, name',
            defaultValue: "Quick old fashioned hazing for the 20 new summer interns"
        },
        assigneeLabel: {
            label: "Assignee Label",
            type: "static",
            defaultValue: "Assigned To"
        },
        assigneeImage: {
            label: "Assignee Picture",
            type: "dynamic",
            accepts: 'image',
            defaultValue: "https://images.unsplash.com/photo-1586367751368-99141fd186a0?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ"
        },
        assigneeName: {
            label: "Assignee Name",
            type: "dynamic",
            accepts: 'name',
            defaultValue: "Frank Abel"
        },
        reviewersLabel: {
            label: "Reviewers Label",
            type: "static",
            defaultValue: "Reviewers"
        },
        reviewers: {
            label: "Reviewers",
            type: "dynamic",
            accepts: 'image',
            accepts: 'multi-reference | image',
            defaultValue: [
                "https://images.unsplash.com/photo-1542513217-0b0eedf7005d?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ",
                "https://images.unsplash.com/photo-1546672741-d327539d5f13?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ",
                "https://images.unsplash.com/photo-1587492520470-8cea42e7b7fe?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjE2MTY1fQ",
            ],
        },
    }
}