import { isEmpty, isEmptyArray, isNullOrUndefined } from './helpers'

// 👉 Required Validator
export const requiredValidator = (value: unknown) => {
  if (isNullOrUndefined(value) || isEmptyArray(value) || value === false)
    return 'សូមបញ្ចូលទិន្នន័យ'

  return !!String(value).trim().length || 'សូមបញ្ចូលទិន្នន័យ'
}

// 👉 Email Validator
export const emailValidator = (value: unknown) => {
  if (isEmpty(value))
    return true

  const re = /^(?:[^<>()[\]\\.,;:\s@"]+(?:\.[^<>()[\]\\.,;:\s@"]+)*|".+")@(?:\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\]|(?:[a-z\-\d]+\.)+[a-z]{2,})$/i

  if (Array.isArray(value))
    return value.every(val => re.test(String(val))) || 'សូមបញ្ចូលអ៊ីម៉េលអោយបានត្រឹមត្រូវ'

  return re.test(String(value)) || 'សូមបញ្ចូលអ៊ីម៉េលអោយបានត្រឹមត្រូវ'
}

// 👉 Password Validator
export const passwordValidator = (password: string) => {
  const regExp = /^[A-Za-z\d@$!%*?&]{6,}$/

  const validPassword = regExp.test(password)

  return validPassword || 'ពាក្យសម្ងាត់ត្រូវតែមានចំនួនតួអក្សរចាប់ពី ៦ខ្ទង់ឡើង'
}

// 👉 Confirm Password Validator
export const confirmedValidator = (value: string, target: string) =>

  value === target || 'ការបញ្ជាក់ពាក្យសម្ងាត់មិនត្រឹមត្រូវទេ'

// 👉 Between Validator
export const betweenValidator = (value: unknown, min: number, max: number) => {
  const valueAsNumber = Number(value)

  return (Number(min) <= valueAsNumber && Number(max) >= valueAsNumber) || `សូមបញ្ចូលលេខចន្លោះពី ${min} និង ${max}`
}

// 👉 Integer Validator
export const integerValidator = (value: unknown) => {
  if (isEmpty(value))
    return true

  if (Array.isArray(value))
    return value.every(val => /^-?\d+$/.test(String(val))) || 'សូមបញ្ចូលលេខគត់'

  return /^-?\d+$/.test(String(value)) || 'សូមបញ្ចូលលេខគត់'
}

// 👉 Regex Validator
export const regexValidator = (value: unknown, regex: RegExp | string): string | boolean => {
  if (isEmpty(value))
    return true

  let regeX = regex
  if (typeof regeX === 'string')
    regeX = new RegExp(regeX)

  if (Array.isArray(value))
    return value.every(val => regexValidator(val, regeX))

  return regeX.test(String(value)) || 'ទំរង់ទិន្នន័យនេះមិនត្រឹមត្រូវទេ'
}

// 👉 Alpha Validator
export const alphaValidator = (value: unknown) => {
  if (isEmpty(value))
    return true

  return /^[A-Z]*$/i.test(String(value)) || 'វាលនេះអាចមានតែអក្សរអក្សរក្រមប៉ុណ្ណោះ'
}

// 👉 URL Validator
export const urlValidator = (value: unknown) => {
  if (isEmpty(value))
    return true

  const re = /^https?:\/\/[^\s$.?#].\S*$/

  return re.test(String(value)) || 'URL មិនត្រឹមត្រូវ'
}

// 👉 Length Validator
export const lengthValidator = (value: unknown, length: number) => {
  if (isEmpty(value))
    return true

  return String(value).length === length || `"ប្រវែងតួអក្សរត្រូវតែមាន ${length} តួអក្សរ"`
}

// 👉 Alpha-dash Validator
export const alphaDashValidator = (value: unknown) => {
  if (isEmpty(value))
    return true

  const valueAsString = String(value)

  return /^[\w-]*$/.test(valueAsString) || 'តួអក្សរខុស'
}
