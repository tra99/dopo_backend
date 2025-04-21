// validator for checking if the date is in the future
export function futureDateValidator(date: string): string | true {
    // if (!date) return "សូមបញ្ជូលថ្ញៃខែ";

    const today = new Date();
    today.setHours(0, 0, 0, 0); // Normalize today to midnight
    const inputDate = new Date(date + "T00:00:00Z"); // Parse input as UTC

    if (isNaN(inputDate.getTime())) return "ទំរង់មិនត្រូវ"; // Handle invalid dates
    if (inputDate <= today) return "ថ្ងៃជាអនាគត";

    return true; // Validation passed
}



export default {
    futureDateValidator,
}
