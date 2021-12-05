<script>

import MenuIcon from 'vue-material-design-icons/Menu';
import Name from 'vue-material-design-icons/AccountBox';
import Email from 'vue-material-design-icons/Email';
import Password from 'vue-material-design-icons/Lock';
import Phone from 'vue-material-design-icons/Phone';
import Image from 'vue-material-design-icons/Image';
import Video from 'vue-material-design-icons/Youtube';
import File from 'vue-material-design-icons/File';
import Link from 'vue-material-design-icons/Link';
import Text from 'vue-material-design-icons/FormatAlignLeft';
import LongText from 'vue-material-design-icons/FormatIndentIncrease';
import Number from 'vue-material-design-icons/Numeric';
import Switch from 'vue-material-design-icons/ToggleSwitch';
import Date from 'vue-material-design-icons/Calendar';
import Location from 'vue-material-design-icons/MapMarker';
import Rating from 'vue-material-design-icons/StarFace';
import Reference from 'vue-material-design-icons/DatabaseImport';
import MultiReference from 'vue-material-design-icons/DatabaseSearch';

import ArrowDown from 'vue-material-design-icons/ArrowDown';
import Delete from 'vue-material-design-icons/Delete';
import Add from 'vue-material-design-icons/Plus';
import Status from 'vue-material-design-icons/CheckboxMultipleMarkedCircle';
import Color from 'vue-material-design-icons/FormatColorFill';

export default {
    name: "MdICon",
    props: {
        icon: String,
        size: {
            type: String | Number,
            default: 28
        }
    },
    render (h) {
        switch (this.icon) {
            case 'add':
                return <Add color="currentColor" size={this.size} />;
                
            case 'down':
                return <ArrowDown color="currentColor" size={this.size} />;
                
            case 'name':
                return <Name color="currentColor" size={this.size} />;

            case 'email':
                return <Email color="currentColor" size={this.size} />;

            case 'password':
                return <Password color="currentColor" size={this.size} />;
                
            case 'phone':
                return <Phone color="currentColor" size={this.size} />;

            case 'image':
                return <Image color="currentColor" size={this.size} />;
                
            case 'video':
                return <Video color="currentColor" size={this.size} />;
                
            case 'file':
                return <File color="currentColor" size={this.size} />;
                
            case 'link':
                return <Link color="currentColor" size={this.size} />;
                
            case 'location':
                return <Location color="currentColor" size={this.size} />;
                
            case 'rating':
                return <Rating color="currentColor" size={this.size} />;
            
            case 'reference':
                return <Reference color="currentColor" size={this.size} />;

            case 'multi-reference':
                return <MultiReference color="currentColor" size={this.size} />;
                
            case 'long text':
                return <LongText color="currentColor" size={this.size} />;
                
            case 'string':
                return <Text color="currentColor" size={this.size} />;

            case 'status':
                return <Status color="currentColor" size={this.size} />;
                
            case 'number':
                return <Number color="currentColor" size={this.size} />;
                
            case 'boolean':
                return <Switch color="currentColor" size={this.size} />;

            case 'color':
                return <Color color="currentColor" size={this.size} />;
                
            case 'date':
                return <Date color="currentColor" size={this.size} />;
                
            case 'delete':
                return <Delete color="currentColor" size={this.size} />;
            
            default:
                return <span>{this.icon}</span>
        }
    }
}
</script>