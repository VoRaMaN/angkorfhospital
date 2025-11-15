use App\Enums\PrescriptionStatusEnum;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePrescriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $prescription = $this->route('prescription');
        $user = $this->user();

        // Admin users can update any prescription
        if ($user->hasRole('admin')) {
            return true;
        }

        // Only doctors can update prescriptions
        if (!$user->hasRole('Doctor')) {
            return false;
        }

        // Only the doctor who prescribed it can update the prescription
        return $prescription->doctor_id === $user->staff->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_id' => 'sometimes|exists:patients,id',
            'doctor_id' => 'sometimes|exists:staff,id',
            'medication_id' => 'sometimes|exists:medications,id',
            'dosage' => 'sometimes|string|max:100',
            'frequency' => 'nullable|string|max:100',
            'duration' => 'nullable|string|max:100',
            'instructions' => 'sometimes|string|max:1000',
            'prescribed_date' => 'sometimes|date|before_or_equal:today',
            'status' => 'sometimes|in:' . implode(',', array_column(PrescriptionStatusEnum::cases(), 'value')),
        ];
    }
}
