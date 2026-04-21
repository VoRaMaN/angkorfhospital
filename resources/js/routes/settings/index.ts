import userManagement from './user-management'
import roles from './roles'
import patches from './patches'
import packageItems from './package-items'
import specialItems from './special-items'
import labItems from './lab-items'
import medicines from './medicines'
const settings = {
    userManagement: Object.assign(userManagement, userManagement),
roles: Object.assign(roles, roles),
patches: Object.assign(patches, patches),
packageItems: Object.assign(packageItems, packageItems),
specialItems: Object.assign(specialItems, specialItems),
labItems: Object.assign(labItems, labItems),
medicines: Object.assign(medicines, medicines),
}

export default settings