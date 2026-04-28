import { adToBs } from '@sbmdkl/nepali-date-converter';

/**
 * Returns today's date in Bikram Sambat (BS) format: YYYY-MM-DD
 */
export function getTodayBS(): string {
    const today = new Date();
    const formattedAd = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
    return adToBs(formattedAd);
}
