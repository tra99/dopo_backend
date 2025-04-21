export const paginationMeta = <T extends { page: number; itemsPerPage: number }>(options: T, total: number) => {
  const start = (options.page - 1) * options.itemsPerPage + 1
  const end = Math.min(options.page * options.itemsPerPage, total)

  return `បង្ហាញចាប់ពី ${
    total === 0 ? convertToKhmerNumerals('0') : convertToKhmerNumerals(start)} 
    ទៅ ${ end === 0 ? convertToKhmerNumerals('0') : convertToKhmerNumerals(end)} 
    នៃចំនួនសរុប ${ total === 0 ? convertToKhmerNumerals('0') : convertToKhmerNumerals(total)}`
}
