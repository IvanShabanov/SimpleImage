<?php
class SimpleImage
{
	private
		$image,
		$image_type = false,
		$last_error = '';

	/**
	 * Method load
	 * Load image
	 * Загрузит картинку
	 * @param  $filename - имя файла
	 *
	 * @return bool
	 */
	public function load($filename): bool
	{
		$result = true;
		$image_info = getimagesize($filename);
		$this->image_type = $image_info[2];
		if ($this->image_type == IMAGETYPE_JPEG) {
			try {
				$this->image = imagecreatefromjpeg($filename);
			} catch (Exception $e) {
				$this->last_error = $e->getMessage();
				$this->image_type = false;
				$result = false;
			}
		} elseif ($this->image_type == IMAGETYPE_GIF) {
			try {
				$this->image = imagecreatefromgif($filename);
			} catch (Exception $e) {
				$this->last_error = $e->getMessage();
				$this->image_type = false;
				$result = false;
			}
		} elseif ($this->image_type == IMAGETYPE_PNG) {
			try {
				$this->image = imagecreatefrompng($filename);
				imagealphablending($this->image, false);
				imagesavealpha($this->image, true);
			} catch (Exception $e) {
				$this->last_error = $e->getMessage();
				$this->image_type = false;
				$result = false;
			}
		} elseif ($this->image_type == IMAGETYPE_WEBP) {
			try {
				$this->image = imagecreatefromwebp($filename);
				imagealphablending($this->image, false);
				imagesavealpha($this->image, true);
			} catch (Exception $e) {
				$this->last_error = $e->getMessage();
				$this->image_type = false;
				$result = false;
			}
		} elseif ($this->image_type == IMAGETYPE_AVIF) {
			try {
				$this->image = imagecreatefromavif($filename);
				imagealphablending($this->image, false);
				imagesavealpha($this->image, true);
			} catch (Exception $e) {
				$this->last_error = $e->getMessage();
				$this->image_type = false;
				$result = false;
			}
		} else {
			$this->last_error = 'File is not image';
			$this->image_type = false;
			$result           = false;
		}
		return $result;
	}

	/**
	 * Method getLastError
	 *
	 * @return string
	 */
	public function getLastError(): string
	{
		return $this->last_error;
	}


	/**
	 * Method пetImage
	 * получить экземпляр изображения
	 */
	public function getImage(): bool | object
	{
		if (!$this->image_type) {
			return false;
		}
		return $this->image;
	}

	/**
	 * Method setImage
	 * установить изображение
	 * @param  $img - объект GD image
	 * @param  $img_type - тип изображения
	 *
	 * @return bool
	 */
	public function setImage($img, $img_type = IMAGETYPE_PNG): bool
	{
		if (!$this->isGdImage($img)) {
			$this->last_error = '$img is not GD object';
			return false;
		}
		$this->image = $img;
		$this->image_type = $img_type;
		return true;
	}


	/**
	 * Method isGdImage
	 * Проверит является ли $img объектом GDImage
	 * @param $img - объект для проверки
	 *
	 * @return bool
	 */
	public function isGdImage($img): bool
	{
		return (gettype($img) == "object" && get_class($img) == "GdImage");
	}

	/**
	 * Method newImage
	 * Создать новый объект image
	 * @param $w - Ширина
	 * @param $h - Высота
	 * @param $img_type - Тип изображения
	 *
	 * @return bool
	 */
	public function newImage($w, $h, $img_type = IMAGETYPE_PNG): bool
	{
		$result = true;
		try {
			$this->image = imagecreatetruecolor($w, $h);
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
			$this->image_type = $img_type;
		} catch (Exception $e) {
			$this->last_error = $e->getMessage();
			$result = false;
		}
		return $result;
	}

	/**
	 * Method save
	 * Сохранит изображение
	 * @param $filename - filename (имя файла)
	 * @param $image_type - type (тип файла) (IMAGETYPE_JPEG / IMAGETYPE_GIF / IMAGETYPE_PNG / IMAGETYPE_WEBP / IMAGETYPE_AVIF)
	 * @param $compression - compression Jpeg/Webp (компрессия для Jpeg и Webp)
	 * @param $permissions - permissions (Права доступа к файлу)
	 *
	 * @return bool
	 */
	public function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 75, $permissions = null): bool
	{
		$result = false;
		if ($image_type == IMAGETYPE_JPEG) {
			try {
				imagejpeg($this->image, $filename, $compression);
				$result = true;
			} catch (Exception $e) {
				$this->last_error = $e->getMessage();
				$result = false;
			}
		} elseif ($image_type == IMAGETYPE_GIF) {
			try {
				imagegif($this->image, $filename);
				$result = true;
			} catch (Exception $e) {
				$this->last_error = $e->getMessage();
				$result = false;
			}
		} elseif ($image_type == IMAGETYPE_PNG) {
			try {
				imagepng($this->image, $filename);
				$result = true;
			} catch (Exception $e) {
				$this->last_error = $e->getMessage();
				$result = false;
			}
		} elseif ($image_type == IMAGETYPE_WEBP) {
			try {
				imagewebp($this->image, $filename, $compression);
				$result = true;
			} catch (Exception $e) {
				$this->last_error = $e->getMessage();
				$result = false;
			}
		} elseif ($image_type == IMAGETYPE_AVIF) {
			try {
				imageavif($this->image, $filename, $compression);
				$result = true;
			} catch (Exception $e) {
				$this->last_error = $e->getMessage();
				$result = false;
			}
		} else {
			$this->last_error = 'Image type unsupported';
			$result = false;
		}
		if (file_exists($filename)) {
			if ($permissions != null) {
				chmod($filename, $permissions);
			}
			if (filesize($filename) > 0) {
				$result = true;
			}
		} else {
			$this->last_error = 'Cant save file';
		}
		return $result;
	}

	/**
	 * Method output
	 * Вывод изображения в браузер
	 * @param $image_type - type image (Тип изображения) (IMAGETYPE_JPEG / IMAGETYPE_GIF / IMAGETYPE_PNG / IMAGETYPE_WEBP)
	 *
	 * @return bool
	 */
	public function output($image_type = IMAGETYPE_JPEG): bool
	{
		$result = false;
		if ($image_type == IMAGETYPE_JPEG) {
			try {
				imagejpeg($this->image);
				$result = true;
			} catch (Exception $e) {
				$this->last_error = $e->getMessage();
				$result = false;
			}
		} elseif ($image_type == IMAGETYPE_GIF) {
			try {
				imagegif($this->image);
				$result = true;
			} catch (Exception $e) {
				$this->last_error = $e->getMessage();
				$result = false;
			}
		} elseif ($image_type == IMAGETYPE_PNG) {
			try {
				imagepng($this->image);
				$result = true;
			} catch (Exception $e) {
				$this->last_error = $e->getMessage();
				$result = false;
			}
		} elseif ($image_type == IMAGETYPE_WEBP) {
			try {
				imagewebp($this->image);
				$result = true;
			} catch (Exception $e) {
				$this->last_error = $e->getMessage();
				$result = false;
			}
		} elseif ($image_type == IMAGETYPE_AVIF) {
			try {
				imageavif($this->image);
				$result = true;
			} catch (Exception $e) {
				$this->last_error = $e->getMessage();
				$result = false;
			}
		} else {
			return $result;
		}
		return $result;
	}

	/**
	 * Method getWidth
	 * Вернет ширину изображения
	 * @return bool|int
	 */
	public function getWidth(): bool | int
	{
		if (!$this->image_type) {
			return false;
		}
		return imagesx($this->image);
	}

	/**
	 * Method getHeight
	 * Вернет высоту изображения
	 * @return bool|int height of loaded image
	 */
	public function getHeight(): bool | int
	{
		if (!$this->image_type) {
			return false;
		}
		return imagesy($this->image);
	}

	/**
	 * Method resizeToHeight
	 * Resize/Scale image to height
	 * Масщтабирует изображение до определенной высоты
	 * @param $height - px (высота)
	 *
	 * @return bool
	 */
	public function resizeToHeight($height): bool
	{
		if (!$this->image_type) {
			return false;
		}
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		return $this->resize($width, $height);
	}

	/**
	 * Method resizeToWidth
	 * Resize/Scale image to width
	 * Масштабирует изображение до определенной ширины
	 * @param $width - px (Ширина)
	 *
	 * @return bool
	 */
	public function resizeToWidth($width): bool
	{
		if (!$this->image_type) {
			return false;
		};
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		return $this->resize($width, $height);
	}

	/**
	 * Method scale
	 * Percent Scaling
	 * Масштабирует по процентному соотношению
	 * @param $scale - percent (процент)
	 *
	 * @return bool
	 */
	public function scale($scale): bool
	{
		if (!$this->image_type) {
			return false;
		};
		$width = $this->getWidth() * $scale / 100;
		$height = $this->getheight() * $scale / 100;
		return $this->resize($width, $height);
	}

	/**
	 * Method resize
	 * Resize/Scale image
	 * Масштабирует изображение
	 * @param $width - px (Ширина)
	 * @param $height - px (высота)
	 *
	 * @return bool
	 */
	public function resize($width, $height): bool
	{
		$result = false;
		if (!$this->image_type) {
			return $result;
		};
		try {
			$new_image = imagecreatetruecolor($width, $height);
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
			$this->image = $new_image;
			$result = true;
		} catch (Exception $e) {
			$this->last_error = $e->getMessage();
			$result = false;
		}
		return $result;
	}

	/**
	 * Method cover
	 * Resize/Scale image to cover area
	 * Масштабирует изображение чтобы заполнить область
	 * @param $width - px (Ширина)
	 * @param $height - px (высота)
	 *
	 * @return bool
	 */
	public function cover($width, $height): bool
	{
		if (!$this->image_type) {
			return false;
		};
		$w = $this->getWidth();
		if ($width != $w) {
			$this->resizeToWidth($width);
		}
		$h = $this->getHeight();
		if ($height > $h) {
			$this->resizeToHeight($height);
		}
		return $this->wrapInTo($width, $height);
	}

	/**
	 * Method wrapInTo
	 * Wrap image to area
	 * Обрезает все что не вмещается в область
	 * @param $width - px (Ширина)
	 * @param $height - px (высота)
	 *
	 * @return bool
	 */
	public function wrapInTo($width, $height): bool
	{
		$result = false;
		if (!$this->image_type) {
			return $result;
		}
		try {
			$new_image = imagecreatetruecolor($width, $height);
			$w = $this->getWidth();
			$h = $this->getHeight();
			if ($width > $w) {
				$dst_x = round(($width - $w) / 2);
				$src_x = 0;
				$dst_w = $w;
				$src_w = $w;
			} else {
				$dst_x = 0;
				$src_x = round(($w - $width) / 2);
				$dst_w = $width;
				$src_w = $width;
			}
			if ($height > $h) {
				$dst_y = round(($height - $h) / 2);
				$src_y = 0;
				$dst_h = $h;
				$src_h = $h;
			} else {
				$dst_y = 0;
				$src_y = round(($h - $height) / 2);
				$dst_h = $height;
				$src_h = $height;
			}
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			$transparentindex = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagefill($new_image, 0, 0, $transparentindex);
			imagecopyresampled($new_image, $this->image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
			$this->image = $new_image;
			$result = true;
		} catch (Exception $e) {
			$this->last_error = $e->getMessage();
			$result = false;
		}
		return $result;
	}

	/**
	 * Method resizeInTo
	 * Resize/Scale image in to area
	 * Масштабюировать чтобы изображение влезло в рамки
	 * @param $width - px (Ширина)
	 * @param $height - px (высота)
	 *
	 * @return bool
	 */
	public function resizeInTo($width, $height): bool
	{
		if (!$this->image_type) {
			return false;
		}
		$ratiow = $width / $this->getWidth() * 100;
		$ratioh = $height / $this->getHeight() * 100;
		$ratio = min($ratiow, $ratioh);
		return $this->scale($ratio);
	}


	/**
	 * Method smallTo
	 * Resize/Scale image in to area if this bigger
	 * Уменьшает изображение если текущее больше
	 * @param $width - px (Ширина)
	 * @param $height - px (высота)
	 *
	 * @return bool
	 */
	public function smallTo($width, $height): bool
	{
		if (!$this->image_type) {
			return false;
		}
		if (($this->getWidth() > $width) or ($this->getHeight() > $height)) {
			return $this->resizeInTo($width, $height);
		} else {
			return false;
		}
	}

	/**
	 * Method crop
	 * Crop loaded image by coordinates
	 * Вырезать кусок по координатам углов
	 * @param $x1, $y1, $x2, $y2 - coordinates (координаты углов)
	 *
	 * @return bool
	 */
	public function crop($x1, $y1, $x2, $y2): bool
	{
		$result = false;
		if (!$this->image_type) {
			return $result;
		}
		try {
			$w = abs($x2 - $x1);
			$h = abs($y2 - $y1);
			$x = min($x1, $x2);
			$y = min($y1, $y2);
			$new_image = imagecreatetruecolor($w, $h);
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			imagecopy($new_image, $this->image, 0, 0, $x, $y, $w, $h);
			$this->image = $new_image;
			$result = true;
		} catch (Exception $e) {
			$this->last_error = $e->getMessage();
			$result = false;
		}
		return $result;
	}

	/**
	 * Method setBackgroundColor
	 * Установит задний фон на прозрачные области
	 * @param $red 0-255
	 * @param $green 0-255
	 * @param $blue 0-255
	 *
	 * @return bool
	 */
	public function setBackgroundColor($red, $green, $blue): bool
	{
		$result = false;
		if (!$this->image) {
			return $result;
		}
		try {
			$w         = (int) $this->getWidth();
			$h         = (int) $this->getHeight();
			$new_image = imagecreatetruecolor($w, $h);
			$color     = imagecolorallocate($new_image, $red, $green, $blue);
			imagefilledrectangle($new_image, 0, 0, $w, $h, $color);
			imagecopy($new_image, $this->image, 0, 0, 0, 0, $w, $h);
			$this->image = $new_image;
			$result = true;
		} catch (Exception $e) {
			$this->last_error = $e->getMessage();
			$result = false;
		}
		return $result;
	}

	/**
	 * Method fill
	 * Закрасит всю област изображения цветом
	 * @param $red  0-255
	 * @param $green 0-255
	 * @param $blue 0-255
	 *
	 * @return bool
	 */
	public function fill($red, $green, $blue): bool
	{
		$result = false;
		if (!$this->image) {
			return $result;
		}
		try {
			$w     = (int) $this->getWidth();
			$h     = (int) $this->getHeight();
			$color = imagecolorallocate($this->image, $red, $green, $blue);
			imagefilledrectangle($this->image, 0, 0, $w, $h, $color);
			$result = true;
		} catch (Exception $e) {
			$this->last_error = $e->getMessage();
			$result = false;
		}
		return $result;
	}

	/**
	 * Method watermark
	 * Наложить на изображение другое изображение
	 * @param $img объект GD IMAGE вставляемого избражения
	 * @param $x координаты
	 * @param $y координаты
	 *
	 * @return bool
	 */
	public function watermark($img, $x, $y): bool
	{
		$result = false;
		if (!$this->image) {
			return $result;
		}
		if (!$this->isGdImage($img)) {
			$this->last_error = '$img is not GD object';
			return $result;
		}
		try {
			$w = imagesx($img);
			$h = imagesy($img);
			imagecopy($this->image, $img, $x, $y, 0, 0, $w, $h);
			$result = true;
		} catch (Exception $e) {
			$this->last_error = $e->getMessage();
			$result = false;
		}
		return $result;
	}

	/**
	 * Method setOpacity
	 * Установить прозначьность изображения
	 * @param $opacity 0-1
	 *
	 * @return bool
	 */
	public function setOpacity($opacity): bool
	{
		$result = false;
		if (!$this->image) {
			return $result;
		}
		try {
			$transparency = 1 - $opacity;
			imagefilter($this->image, IMG_FILTER_COLORIZE, 0, 0, 0, 127 * $transparency);
			$result = true;
		} catch (Exception $e) {
			$this->last_error = $e->getMessage();
			$result = false;
		}
		return $result;
	}
}
