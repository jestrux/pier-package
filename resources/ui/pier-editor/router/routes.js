import DefaultContainer from '../UI/DefaultContainer';
import Models from '../UI/pages/Models';
import ModelsList from '../UI/pages/Models/List';
import AddModel from '../UI/pages/Models/Add';
import ModelDetail from '../UI/pages/Models/Detail';
import AddField from '../UI/pages/Models/AddField';

export default [
    {
        path: '/',
        name: 'Pier',
        redirect: '/models',
        component: DefaultContainer,
        children: [
            {
                path: 'models',
                name: 'Models',
                component: Models,
                // redirect: '/models/list',
                children: [
                    {
                        // path: 'list',
                        path: '/',
                        name: 'ModelsList',
                        component: ModelsList
                    },
                    {
                        path: 'add',
                        name: 'AddModel',
                        component: AddModel
                    },
                    {
                        path: ':modelId/details',
                        name: 'Model Details',
                        component: ModelDetail,
                        props: true
                    },
                    {
                        path: ':modelId/addField',
                        name: 'Add Field To Model',
                        component: AddField,
                        props: true
                    }
                ]
            }
        ]
    }
]