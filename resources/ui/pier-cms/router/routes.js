import PierCMSWrapper from '../UI/PierCMSWrapper';
import PierCMSList from '../UI/PierCMS';
import Detail from '../UI/Detail';
import AddRow from '../UI/AddRow';
import EditRow from '../UI/EditRow';
import DeleteRow from '../UI/DeleteRow';
const PierCMSContent = {
    template: `<router-view />`
};

export default [
    {
        path: '/',
        name: 'PierCMS',
        component: PierCMSWrapper,
        children: [
            {
                path: '/:modelName',
                redirect: '/:modelName/list/',
                name: 'Model',
                component: PierCMSContent,
                children: [
                    {
                        path: '/:modelName/list/',
                        name: 'Model List',
                        component: PierCMSList,
                        props: true,
                        children: [
                            {
                                path: '/:modelName/list/add',
                                name: 'Add Row',
                                component: AddRow
                            },
                            {
                                path: '/:modelName/list/:rowId/edit',
                                name: 'Edit Row',
                                component: EditRow,
                                props: true
                            },
                            {
                                path: '/:modelName/list/:rowId/delete',
                                name: 'Delete Row',
                                component: DeleteRow,
                                props: true
                            }
                        ]
                    },
                    {
                        path: '/:modelName/detail/:rowId/',
                        name: 'View Row',
                        component: Detail,
                        props: true
                    },
                ]
            }
        ]
    }
]