import { format } from 'date-fns'

export function useHelpers() {
  const formatDate = (date: string, time = false) => {
    if (!date) return '';
    return format(new Date(date), time ? 'MMM d, yyyy hh:mm b' : 'MMM d, yyyy');
  }

  const formatDateRange = (start: string, end: string) => {
    if (!start || !end) return ''
    return `${format(new Date(start), 'MMM d')} to ${format(new Date(end), 'MMM d, yyyy')}`
  }

  const avatar = (fname: string, lname: string) => {
    return `https://ui-avatars.com/api/?background=4961fe&color=fff&bold=true&name=${fname}+${lname}`;
  }

  const highlightMentions = (message: string) => {
    return message.replace(/(@[A-Za-z]+(?:\s[A-Za-z]+)?)/g, '<span class="font-medium text-blue-500">$1</span>');
  }

  const formatMoney = (value: number | string) => {
    const num = Number(value)
    if (isNaN(num)) return value
    return num.toLocaleString()
  }

  return {
    formatDate,
    formatDateRange,
    avatar,
    highlightMentions,
    formatMoney
  }
}
