export async function fetchCategoryApi() {
  const response = await fetch('../actions/categories/get_category.php');
  return response.json();
}