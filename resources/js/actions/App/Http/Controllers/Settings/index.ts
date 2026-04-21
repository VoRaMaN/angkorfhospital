import ProfileController from './ProfileController'
import PasswordController from './PasswordController'
import TwoFactorAuthenticationController from './TwoFactorAuthenticationController'
import UserManagementController from './UserManagementController'
import PatchController from './PatchController'
import PackageItemController from './PackageItemController'
import SpecialItemController from './SpecialItemController'
import LabItemController from './LabItemController'
import MedicineController from './MedicineController'
const Settings = {
    ProfileController: Object.assign(ProfileController, ProfileController),
PasswordController: Object.assign(PasswordController, PasswordController),
TwoFactorAuthenticationController: Object.assign(TwoFactorAuthenticationController, TwoFactorAuthenticationController),
UserManagementController: Object.assign(UserManagementController, UserManagementController),
PatchController: Object.assign(PatchController, PatchController),
PackageItemController: Object.assign(PackageItemController, PackageItemController),
SpecialItemController: Object.assign(SpecialItemController, SpecialItemController),
LabItemController: Object.assign(LabItemController, LabItemController),
MedicineController: Object.assign(MedicineController, MedicineController),
}

export default Settings