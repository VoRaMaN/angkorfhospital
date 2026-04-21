import DashboardController from './DashboardController'
import Settings from './Settings'
import RoleController from './RoleController'
import AppointmentController from './AppointmentController'
import BillingController from './BillingController'
import DepartmentController from './DepartmentController'
import DoctorController from './DoctorController'
import InventoryController from './InventoryController'
import LabPanelController from './LabPanelController'
import MedicineGroupController from './MedicineGroupController'
import SpecialItemController from './SpecialItemController'
import MedicalOrderController from './MedicalOrderController'
import MedicalRecordController from './MedicalRecordController'
import MedicalServiceController from './MedicalServiceController'
import MedicineReportController from './MedicineReportController'
import BillingReportController from './BillingReportController'
import PatientController from './PatientController'
import StaffController from './StaffController'
import FileController from './FileController'
import PatientFileController from './PatientFileController'
import StaffFileController from './StaffFileController'
import VisitController from './VisitController'
import ActivityLogController from './ActivityLogController'
const Controllers = {
    DashboardController: Object.assign(DashboardController, DashboardController),
Settings: Object.assign(Settings, Settings),
RoleController: Object.assign(RoleController, RoleController),
AppointmentController: Object.assign(AppointmentController, AppointmentController),
BillingController: Object.assign(BillingController, BillingController),
DepartmentController: Object.assign(DepartmentController, DepartmentController),
DoctorController: Object.assign(DoctorController, DoctorController),
InventoryController: Object.assign(InventoryController, InventoryController),
LabPanelController: Object.assign(LabPanelController, LabPanelController),
MedicineGroupController: Object.assign(MedicineGroupController, MedicineGroupController),
SpecialItemController: Object.assign(SpecialItemController, SpecialItemController),
MedicalOrderController: Object.assign(MedicalOrderController, MedicalOrderController),
MedicalRecordController: Object.assign(MedicalRecordController, MedicalRecordController),
MedicalServiceController: Object.assign(MedicalServiceController, MedicalServiceController),
MedicineReportController: Object.assign(MedicineReportController, MedicineReportController),
BillingReportController: Object.assign(BillingReportController, BillingReportController),
PatientController: Object.assign(PatientController, PatientController),
StaffController: Object.assign(StaffController, StaffController),
FileController: Object.assign(FileController, FileController),
PatientFileController: Object.assign(PatientFileController, PatientFileController),
StaffFileController: Object.assign(StaffFileController, StaffFileController),
VisitController: Object.assign(VisitController, VisitController),
ActivityLogController: Object.assign(ActivityLogController, ActivityLogController),
}

export default Controllers