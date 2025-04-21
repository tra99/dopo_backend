import { format } from 'date-fns/format';
import { isToday } from 'date-fns/isToday';
import { isYesterday } from 'date-fns/isYesterday';
import { parseISO } from 'date-fns/parseISO';

import { km } from 'date-fns/locale/km';

export const dateFormatter = {
    /**
     * 
     * @param dateString 2025-02-10 07:18:31
     * @returns 
     * - ១២ តុលា ២០២៤, ៣:៤៣pm
     * - ម្សិលមិញ, ៣:៤៣pm
     * - ថ្ងៃនេះ, ៣:៤៣pm
     */
    formatDate: (dateString: string) => {
      if(!dateString) return

      const date = parseISO(dateString); // Convert string to Date object
  
      // Offset the date to UTC+7 (Cambodia Time)
      const offsetDate = new Date(date.getTime() + (7 * 60 * 60 * 1000)); // Add 7 hours in milliseconds
    
      // Format the time part using Khmer numerals
      const time = format(offsetDate, 'h:mm a', { locale: km });
  
      // Check if the date is today or yesterday and apply corresponding formatting
      if (isToday(offsetDate)) {
        return `ថ្ងៃនេះ, ${convertToKhmerNumerals(time)}`;
      }
    
      if (isYesterday(offsetDate)) {
        return `ម្សិលមិញ, ${convertToKhmerNumerals(time)}`;
      }
    
      // Format for general past dates (day, month, year, time) and convert the numbers to Khmer numerals
      const day = convertToKhmerNumerals(format(offsetDate, 'd', { locale: km }));
      const month = format(offsetDate, 'MMMM', { locale: km });
      const year = convertToKhmerNumerals(format(offsetDate, 'yyyy', { locale: km }));
      return `${day} ${month} ${year}, ${convertToKhmerNumerals(time)}`;
    },


    /**
     * Compare two dates and return a formatted result.
     * If only the day is different, show the range of days.
     * If month or year is different, show the full date range with a dash between both dates.
     *
     * @param dateString1 2025-02-20
     * @param dateString2 2025-03-15
     * @returns 
     * - 21-24 តុលា 2024 if only the day is different
     */
    compareDates: (dateString1: string, dateString2: string) => {
      if(!dateString1 || !dateString2) return


      const date1 = parseISO(dateString1);
      const date2 = parseISO(dateString2);
  
      // Offset both dates to UTC+7 (Cambodia Time)
      const offsetDate1 = new Date(date1.getTime() + (7 * 60 * 60 * 1000)); 
      const offsetDate2 = new Date(date2.getTime() + (7 * 60 * 60 * 1000)); 
  
      // Get the day, month, and year of both dates in Khmer format
      const day1 = convertToKhmerNumerals(format(offsetDate1, 'd', { locale: km }));
      const day2 = convertToKhmerNumerals(format(offsetDate2, 'd', { locale: km }));
      const month1 = format(offsetDate1, 'MMMM', { locale: km });
      const month2 = format(offsetDate2, 'MMMM', { locale: km });
      const year1 = convertToKhmerNumerals(format(offsetDate1, 'yyyy', { locale: km }));
      const year2 = convertToKhmerNumerals(format(offsetDate2, 'yyyy', { locale: km }));
  
      // Check if only the days are different, month and year are the same
      if (month1 === month2 && year1 === year2) {
        return `${day1}-${day2} ${month1} ${year1}`;
      }

      if(month1 !== month2 && year1 === year2) {
        return `${day1} ${month1} - ${day2} ${month2} ${year1}`;
      }
  
      // If month or year is different, return full date range with a dash
      return `${day1} ${month1} ${year1} - ${day2} ${month2} ${year2}`;
    }
}




// Helper function to convert a number to Khmer numerals
export const convertToKhmerNumerals = (number: string | number) => {
  // Khmer numerals map
  const khmerNumerals: { [key: string]: string } = {
    '0': '០',
    '1': '១',
    '2': '២',
    '3': '៣',
    '4': '៤',
    '5': '៥',
    '6': '៦',
    '7': '៧',
    '8': '៨',
    '9': '៩'
  };

  // Convert number to string to handle both types
  return String(number)
    .split('')
    .map(digit => khmerNumerals[digit] || digit)
    .join('');
};
