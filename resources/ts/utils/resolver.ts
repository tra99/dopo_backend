
export const resolveStatusColor = (stat: string) => {
    if (!stat)
        return 'warning'
    else  return 'success'
}

/**
 * 
 * @param date 2025-02-20 23:59:59 or [2025-02-20, 2025-03-15]
 * @returns 
 * - ថ្ងៃនេះ, ១១:៤០ ព្រឹក
 * - ២០ កុម្ភៈ - ១៥ មីនា ២០២៥
 * - ២០-១៥ មីនា ២០២៥
 */
export const resolveDate = (date: string | string[]) => {
    if (!date)
        return 'មិនបានកំណត់'
    else {
        if(typeof date === 'string')
            return dateFormatter.formatDate(date)
        else
            return dateFormatter.compareDates(date[0], date[1])
    }
}

export const resolveKhmerLetters = (num: number) => {
    const khmerLetters = "កខគឃងចឆជឈញដឋឌឍណតថធនបផពភមយរលវសហអ"; // Add more if needed
    
    return khmerLetters[num] || "";
}

/**
 * Resolves a Cambodian phone number from international format (e.g., ៨៥៥១២៧៧៣៨២២) 
 * to local format (e.g., ០១២ ៧៧៣ ៨២២).
 *
 * - Converts `៨៥៥` (Cambodian country code) to `០` for local dialing.
 * - Formats numbers into `0XX XXX XXX` for 9-digit numbers.
 * - Formats numbers into `0XX XXXXXX` for 8-digit numbers.
 * - Returns the original input if it doesn't match expected formats.
 *
 * @param {string | number} phone - The phone number in Khmer numerals, either as a string or number.
 * @returns {string} The formatted local phone number.
 */
export const resolvePhonenumber = (phone: string | number): string => {
    // Ensure input is a string
    if(typeof phone !== 'string')
        phone = phone.toString();

    phone = convertToKhmerNumerals(phone as string)

    // Replace country code ៨៥៥ with local ០
    if (phone.startsWith("៨៥៥")) {
        phone = "០" + phone.slice(3);
    }

    // Format into 0XX XXX XXX (for 9-digit phones) or 0XX XXXXXX (for 8-digit)
    if (phone.length === 9) {
        return phone.slice(0, 3) + " " + phone.slice(3, 6) + " " + phone.slice(6);
    } else if (phone.length === 8) {
        return phone.slice(0, 3) + " " + phone.slice(3);
    }

    // Return original if it doesn't match expected length
    return phone;
}

export const resolveModelDisplay = (modelName: string) : string => {
    switch (modelName) {
        case 'Answer':
          return 'ចម្លើយ';
        case 'AuditLog':
          return 'កំណត់ហេតុ';
        case 'Category':
          return 'ស្ដង់ដារ និងសូចនករ';
        case 'CategoryGroups':
          return 'ស្ដង់ដារ និងសូចនករ'
        case 'Challenge':
          return 'បញ្ហាប្រឈម'
        case 'Config':
          return 'ការកំណត់';
        case 'Dashboard':
          return 'ផ្ទាំងគ្រប់គ្រង';
        case 'Evaluation':
          return 'បញ្ជីវាយតម្លៃ';
        case 'EvaluationCriteria':
          return 'វាយតម្លៃសំណួរ';
        case 'Mission':
          return 'បេសកកម្ម';
        case 'MissionSchool':
          return 'បេសកកម្មនៃសាលា';
        case 'Notification':
          return 'ការ​ជូនដំណឹង';
        case 'Participant':
          return 'អ្នកចូលរួមបេសកម្ម';
        case 'Question':
          return 'សំណួរ';
        case 'Role':
          return 'តួនាទី';
        case 'School':
          return 'សាលារៀន';
        case 'SchoolParticipants':
          return 'អ្នកចូលរួមតាមសាលា';
        case 'SchoolSurvey':
          return 'ការស្ទង់មតិនៃសាលា';
        case 'SchoolType':
          return 'ប្រភេទសាលា';
        case 'SupportPhase':
          return 'ដំណាក់កាលគាំទ្រ';
        case 'Survey':
          return 'ការស្ទង់មតិ';
        case 'User':
          return 'អ្នកប្រើប្រាស់';
        default:
          // Fallback if none match
          return modelName;
      }
}



export default {
    resolveStatusColor,
    resolveDate,
    resolveKhmerLetters,
    resolvePhonenumber,
    resolveModelDisplay,
}

